<?php

namespace App\Http\Controllers\MobileApp;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeadRequest;
use App\Models\DocumentUpload;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\Leads;
use App\Models\States;
use App\Models\Cities;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Jobs\FirebaseNotification;
use Log;
use Throwable;


class LeadController extends Controller 
{

    public function index(Request $request)
    {
        $user = $request->user();

        // 1. INPUT HANDLING 
        $input = [
            'id'            => $request->input('id'),
            'customerID'    => $request->input('customerID'),
            'leadType'      => $request->input('leadType'), 
            'leadName'      => $request->input('leadName'),
            'leadMobile'    => $request->input('leadMobile'),
            'businessName'  => $request->input('businessName'),
            'state'         => $request->input('state'),
            'city'          => $request->input('city'),
            'startDate'     => $request->input('startDate'),
            'endDate'       => $request->input('endDate'),
            'page'          => $request->input('page', 1),
        ];

        // 2. INITIALIZE THE QUERY, SCOPED TO WHAT THE USER MAY SEE
        $query = Leads::visibleTo($user);

        // 3. APPLYING FILTERS TO THE QUERY
        // The `when` method only applies the filter if the input value is not empty.

        // Lead Type filter
        $query->when($input['leadType'] === 'Y', fn($q) => $q->whereNotNull('customer_id'));
        $query->when($input['leadType'] === 'N', fn($q) => $q->whereNull('customer_id'));

        // Standard 'where' filters
        $query->when($input['id'], fn($q, $id) => $q->where('id', $id));
        $query->when($input['customerID'], fn($q, $id) => $q->where('customer_id', $id));
        $query->when($input['state'], fn($q, $id) => $q->where('state_id', $id));
        $query->when($input['city'], fn($q, $id) => $q->where('city_id', $id));

        // 'like' filters for searching
        $query->when($input['leadMobile'], fn($q, $mobile) => $q->where('mobile', 'like', "%{$mobile}%"));

        // Combined name/business name filter
        if ($input['leadName'] || $input['businessName']) {
            $query->where(function ($q) use ($input) {
                $q->when($input['leadName'], function ($subQuery, $name) {
                    $subQuery->where('name', 'like', "%{$name}%");
                });
                $q->when($input['businessName'], function ($subQuery, $bizName) {
                    // Use orWhere for the second condition
                    $subQuery->orWhere('business_name', 'like', "%{$bizName}%");
                });
            });
        }

        // Date range filter
        if ($input['startDate'] && $input['endDate']) {
            $from = Carbon::parse(substr($input['startDate'], 0, 10))->startOfDay();
            $to = Carbon::parse(substr($input['endDate'], 0, 10))->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        // If only start date is provided, filter from that date to now
        if ($input['startDate'] && !$input['endDate']) {
            $from = Carbon::parse(substr($input['startDate'], 0, 10))->startOfDay();
            $query->where('created_at', '>=', $from);
        }
        // If only end date is provided, filter from the beginning of time to that date
        if (!$input['startDate'] && $input['endDate']) {
            $to = Carbon::parse(substr($input['endDate'], 0, 10))->endOfDay();
            $query->where('created_at', '<=', $to);
        }
      
        // 4. EXECUTE THE QUERY AND PAGINATE
        $perPage    = $request->input('per_page', 10);
        $leads      = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = [];
        $data['leads']          = $leads->map(function ($lead) {
            return [
                'id'                    => $lead->id,
                'name'                  => $lead->name,
                'mobile'                => $lead->mobile,
                'email'                 => $lead->email,
                'creator_id'            => $lead->created_by,
                'lead_created_by'       => User::find($lead->belong_to)->name ?? null,
                'source'                => Helper::getLeadSource($lead->source),
                'lead_source_id'        => $lead->source,
                'business_category'     => Helper::getBusinessCategory($lead->business_category),
                'business_category_id'  => $lead->business_category,
                'business_name'         => $lead->business_name,
                'subscriber'            => User::find($lead->belong_to)->name ?? null,
                'subscriber_id'         => $lead->belong_to,
                'address'               => $lead->address,
                'state'                 => States::find($lead->state_id)->name ?? null,
                'state_id'              => $lead->state_id,
                'city'                  => Cities::find($lead->city_id)->name ?? null,
                'city_id'               => $lead->city_id,
                'postcode'              => $lead->postcode,
                'remarks'               => $lead->remark,
                'created_at'            => $lead->created_at->toDateString(),
                
                ];
        });
        $data['pagination']    = [
            'total'         => $leads->total(),
            'per_page'      => $leads->perPage(),
            'current_page'  => $leads->currentPage(),
            'last_page'     => $leads->lastPage(),  
        ];
        // 5. RETURN THE PAGINATED RESPONSE
        return $this->response_ok($data, 'Leads retrieved successfully.');
    }

    public function getLeadByID(Request $request)
    {
        try {
            $leadId = $request->input('leadID');
            if (!$leadId) {
                return $this->response_failed('Lead ID is required.');
            }

            $lead = Leads::with(['documentUploads', 'tasks'])->find($leadId);
            if (!$lead) {
                return $this->response_failed('Lead not found.');
            }

            $data = [
                'id'                    => $lead->id,
                'name'                  => $lead->name,
                'mobile'                => $lead->mobile,
                'email'                 => $lead->email,
                'creator_id'            => $lead->created_by,
                'lead_created_by'       => User::find($lead->belong_to)->name ?? null,
                'source'                => Helper::getLeadSource($lead->source),
                'lead_source_id'        => $lead->source,
                'business_category'     => Helper::getBusinessCategory($lead->business_category),
                'business_category_id'  => $lead->business_category,
                'business_name'         => $lead->business_name,
                'subscriber'            => User::find($lead->belong_to)->name ?? null,
                'subscriber_id'         => $lead->belong_to,
                'address'               => $lead->address,
                'state'                 => States::find($lead->state_id)->name ?? null,
                'state_id'              => $lead->state_id,
                'city'                  => Cities::find($lead->city_id)->name ?? null,
                'city_id'               => $lead->city_id,
                'postcode'              => $lead->postcode,
                'remarks'               => $lead->remark,
                'attachments'           => Helper::map_document_uploads($lead->documentUploads),
                'created_at'            => $lead->created_at->toDateString(),
            ];

            return $this->response_success($data, 'Lead retrieved successfully.');
        } catch (Throwable $e) {
            return $this->response_failed('Failed to retrieve lead: ' . $e->getMessage());
        } catch (\Exception $f) {
            return $this->response_failed('Failed to retrieve lead: ' . $f->getMessage());
        }
    }

    public function getLeadMedia(Request $request)
    {
        $leadId = $request->input('leadID');

        if (!$leadId) {
            return $this->response_failed('Lead ID is required.');
        }

        $lead = Leads::with('documentUploads')->find($leadId);

        if (!$lead) {
            return $this->response_failed('Lead not found.');
        }

        $data = Helper::media_documents_array($lead->documentUploads);

        return $this->response_success($data, 'Lead media retrieved successfully.');
    }

    public function getLeadTasks(Request $request) {
        $leadId = $request->input('leadID');
        if (!$leadId) {
            return $this->response_failed('Lead ID is required.');
        }

        $lead = Leads::with('tasks')->find($leadId);
        if (!$lead) {
            return $this->response_failed('Lead not found.');
        }

        $data = [];
        $data['tasks']              = $lead->tasks->map(function ($task) {
            return [
                'id'                    => $task->id,
                'title'                 => $task->title,
                'subscriber'            => $task->users->where('role', 2)->first()->user->name ?? null,
                'due_date'              => $task->due_date ?? null,
                'unread_notification'   => count($task->has_unread_notification ?? []),
                'status'                => $task->status,
                'sub_subscribers'       => $task->users->where('role', 6)->pluck('user.name')->toArray(),
                'owners'                => $task->users->where('role', 4)->pluck('user.name')->toArray(),
                'viewers'               => $task->users->where('role', 5)->pluck('user.name')->toArray(),
                'task_start_date'       => $task->start_date ?? null,
                'task_due_date'         => $task->due_date ?? null,
                'task_start_time'       => $task->start_time ?? null,
                'task_due_time'         => $task->due_time ?? null,
            ];
        });

        return $this->response_success($data, 'Lead tasks retrieved successfully.');
    }

    public function getFilterOptions(Request $request)
    {
        $data = [
            'business_category' => Helper::getBusinessCategoryListingForMobile(),
            'lead_source'       => Helper::getLeadSourceListingForMobile(),

            'states'            => Helper::formatForDropdown(
                States::select('id', 'name')->get(),
                'id',
                'name'
            ),
            'cities'            => Helper::formatForDropdown(
                Cities::select('id', 'name', 'state_id')->get(),
                'id',
                'name'
            ),
        ];

        return $this->response_success($data, 'Filter options retrieved successfully.');
    }

    public function addLead(LeadRequest $request)
    {

        $user = $request->user();

        if (!$user->can('create_lead')) {
            $this->response_failed('You do not have permission to create a lead.');
        }

        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            $lead = Leads::create([
                'receiving_date'    => $validatedData['receive_date'], 
                'name'              => $validatedData['name'],           
                'mobile'            => $validatedData['mobile'],         
                'belong_to'         => $user->id,
                'assign_to'         => null,
                'hq_checker'        => $user->seesAllRecords() ? $user->id : null,
                'business_name'     => $validatedData['business_name'] ?? null,
                'customer_id'       => $validatedData['customer_id'] ?? null,
                'source'            => $validatedData['leadsource'] ?? null,
                'business_category' => $validatedData['businesscat'] ?? null,
                'email'             => $validatedData['email'] ?? null,
                'address'           => $validatedData['address'] ?? null,
                'state_id'          => $validatedData['state_id'] ?? null,
                'city_id'           => $validatedData['city_id'] ?? null,
                'postcode'          => $validatedData['postcode'] ?? null,
                'remark'            => $validatedData['remark'] ?? null, 
            ]);
    
            if ($request->hasFile('file')) {
                $this->upload($request, $lead->id);
            }
    
            DB::commit();

            $runJobFirebase = (new FirebaseNotification($lead->id, 'lead', '0'));
            dispatch($runJobFirebase);

            // Reload the lead to include the newly created document uploads
            return $this->response_success(['lead' => $lead], 'Lead created successfully.');

        } catch (Throwable $e) {

            DB::rollBack();
            return $this->response_failed('Failed to create lead: ' . $e->getMessage());

        } catch (\Exception $f) {

            DB::rollBack();
            return $this->response_failed('Failed to create lead: ' . $f->getMessage());
        }        
    }

    public function editLead(LeadRequest $request)
    {
        $user = $request->user();

        if (!$user->can('edit_lead')) {
            return $this->response_failed('You do not have permission to edit a lead.');
        }

        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            $lead = Leads::findOrFail($validatedData['id']);
            $lead->update([
                'receiving_date'    => $validatedData['receive_date'], 
                'name'              => $validatedData['name'],           
                'mobile'            => $validatedData['mobile'],         
                'belong_to'         => $user->id,
                'assign_to'         => null,
                'business_name'     => $validatedData['business_name'] ?? null,
                'customer_id'       => $validatedData['customer_id'] ?? null,
                'source'            => $validatedData['leadsource'] ?? null,
                'business_category' => $validatedData['businesscat'] ?? null,
                'email'             => $validatedData['email'] ?? null,
                'address'           => $validatedData['address'] ?? null,
                'state_id'          => $validatedData['state_id'] ?? null,
                'city_id'           => $validatedData['city_id'] ?? null,
                'postcode'          => $validatedData['postcode'] ?? null,
                'remark'            => $validatedData['remark'] ?? null,
            ]);

            if ($request->has('deleted_attachment_ids')) {
                $idsToDelete = $request->input('deleted_attachment_ids');

                if (is_array($idsToDelete) && !empty($idsToDelete)) {
                    $documents = DocumentUpload::whereIn('id', $idsToDelete)
                        ->where('lead_id', $lead->id)
                        ->get();

                    foreach ($documents as $doc) {
                        if (file_exists($doc->getFileFullPathAttribute())) {
                            unlink($doc->getFileFullPathAttribute());
                        }
                        $doc->delete();
                    }
                }
            }

            // --- 2. Handle NEWLY ADDED files ---
            if ($request->hasFile('file')) {
                $this->upload($request, $lead->id);
            }

            DB::commit();
    
            // if ($request->hasFile('file')) {
            //     // Delete old files
            //     foreach ($lead->documentUploads as $upload) {
            //         if (file_exists($upload->getFileFullPathAttribute())) {
            //             unlink($upload->getFileFullPathAttribute());
            //         }
            //         $upload->delete();
            //     }
    
            //     // Upload new file
            //     $this->upload($request, $lead->id);
            // }
    
            DB::commit();
    
            return $this->response_success(['lead' => $lead], 'Lead updated successfully.');

        } catch (Throwable $e) {

            DB::rollBack();
            return $this->response_failed('Failed to update lead: ' . $e->getMessage());
        } catch (\Exception $f) {
            DB::rollBack();
            return $this->response_failed('Failed to update lead: ' . $f->getMessage());
        }

    }

    private function upload(Request $request, $lead_id)
    {
        $user = $request->user();

        if (!$request->hasFile('file')) {
            return; 
        }

        $files = $request->file('file');

        // Handle both single file and multiple files
        if (!is_array($files)) {
            $files = [$files]; // Convert single file to array for uniform processing
        }

        foreach ($files as $file) {
            // Validate the uploaded file
            if (!$file->isValid()) {
                continue; // Skip invalid files
            }

            // Get original filename and clean it
            $oriname = str_replace(' ', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

            $lead           = Leads::findOrFail($lead_id);
            $filename       = "{$oriname}_{$lead->id}_" . time() . "_" . uniqid() . ".{$file->extension()}";
            $path           = "lead/{$lead->id}";

            // $storedPath = $file->storeAs($path, $filename);
            Storage::putFileAs($path, new File($file), $filename);


            // Create document upload record
            $documentUpload                     = new DocumentUpload();
            $documentUpload->lead_id            = $lead_id;
            $documentUpload->task_id            = NULL;
            $documentUpload->task_comment_id    = NULL;
            $documentUpload->upload_by          = $user->id;
            $documentUpload->filename           = $filename;
            $documentUpload->size               = $file->getSize();
            $documentUpload->mime_type          = $file->getMimeType();
            $documentUpload->save();
        }
    }

    // private function map_document_uploads($uploads)
    // {
    //     return $uploads->map(function ($upload) {
    //         return [
    //             'id'            => $upload->id,
    //             'name'          => $upload->filename,
    //             'size'          => $upload->size,
    //             'file_path'     => $upload->getFileFullPathAttribute(),
    //             'uploaded_by'   => User::find($upload->upload_by)->name ?? 'Unknown',
    //             'created_at'    => $upload->created_at->toDateTimeString(),
    //             'file_type'     => strtolower(pathinfo($upload->filename, PATHINFO_EXTENSION)),
    //             'mime_type'     => $upload->mime_type,
    //         ];
    //     })->toArray();
    // }


    
}


