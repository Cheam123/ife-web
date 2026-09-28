<?php

namespace Database\Seeders;

use App\Models\Cities;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $cities = collect([
            [
                'state_id' => 1,
                'name' => 'Ayer Baloi'
            ],
            [
                'state_id' => 1,
                'name' => 'Ayer Hitam'
            ],
            [
                'state_id' => 1,
                'name' => 'Bakri'
            ],
            [
                'state_id' => 1,
                'name' => 'Bakri'
            ],
            [
                'state_id' => 1,
                'name' => 'Batu Anam'
            ],
            [
                'state_id' => 1,
                'name' => 'Batu Pahat'
            ],
            [
                'state_id' => 1,
                'name' => 'Bekok'
            ],
            [
                'state_id' => 1,
                'name' => 'Benut'
            ],
            [
                'state_id' => 1,
                'name' => 'Bukit Gambir'
            ],
            [
                'state_id' => 1,
                'name' => 'Bukit Pasir'
            ],
            [
                'state_id' => 1,
                'name' => 'Chaah'
            ],
            [
                'state_id' => 1,
                'name' => 'Endau'
            ],
            [
                'state_id' => 1,
                'name' => 'Gelang Patah'
            ],
            [
                'state_id' => 1,
                'name' => 'Gerisek'
            ],
            [
                'state_id' => 1,
                'name' => 'Gugusan Taib Andak'
            ],
            [
                'state_id' => 1,
                'name' => 'Jementah'
            ],
            [
                'state_id' => 1,
                'name' => 'Johor Bahru'
            ],
            [
                'state_id' => 1,
                'name' => 'Kahang'
            ],
            [
                'state_id' => 1,
                'name' => 'Kampung Kenangan Tun Dr Ismail'
            ],
            [
                'state_id' => 1,
                'name' => 'Kluang'
            ],
            [
                'state_id' => 1,
                'name' => 'Kota Tinggi'
            ],
            [
                'state_id' => 1,
                'name' => 'Kukup'
            ],
            [
                'state_id' => 1,
                'name' => 'Kulai'
            ],
            [
                'state_id' => 1,
                'name' => 'Labis'
            ],
            [
                'state_id' => 1,
                'name' => 'Layang Layang'
            ],
            [
                'state_id' => 1,
                'name' => 'Masai'
            ],
            [
                'state_id' => 1,
                'name' => 'Mersing'
            ],
            [
                'state_id' => 1,
                'name' => 'Muar'
            ],
            [
                'state_id' => 1,
                'name' => 'Nusajaya'
            ],
            [
                'state_id' => 1,
                'name' => 'Pagoh'
            ],
            [
                'state_id' => 1,
                'name' => 'Paloh'
            ],
            [
                'state_id' => 1,
                'name' => 'Panchor'
            ],
            [
                'state_id' => 1,
                'name' => 'Parit Jawa'
            ],
            [
                'state_id' => 1,
                'name' => 'Parit Raja'
            ],
            [
                'state_id' => 1,
                'name' => 'Parit Sulong'
            ],
            [
                'state_id' => 1,
                'name' => 'Pasir Gudang'
            ],
            [
                'state_id' => 1,
                'name' => 'Pekan Nanas'
            ],
            [
                'state_id' => 1,
                'name' => 'Pengerang'
            ],
            [
                'state_id' => 1,
                'name' => 'Permas Jaya'
            ],
            [
                'state_id' => 1,
                'name' => 'Plentong'
            ],
            [
                'state_id' => 1,
                'name' => 'Pontian'
            ],
            [
                'state_id' => 1,
                'name' => 'Rengam'
            ],
            [
                'state_id' => 1,
                'name' => 'Rengit'
            ],
            [
                'state_id' => 1,
                'name' => 'Segamat'
            ],
            [
                'state_id' => 1,
                'name' => 'Semerah'
            ],
            [
                'state_id' => 1,
                'name' => 'Senai'
            ],
            [
                'state_id' => 1,
                'name' => 'Senggarang'
            ],
            [
                'state_id' => 1,
                'name' => 'Senibong'
            ],
            [
                'state_id' => 1,
                'name' => 'Seri Gadang'
            ],
            [
                'state_id' => 1,
                'name' => 'Setia Indah'
            ],
            [
                'state_id' => 1,
                'name' => 'Setia Tropika'
            ],
            [
                'state_id' => 1,
                'name' => 'Simpang Rengam'
            ],
            [
                'state_id' => 1,
                'name' => 'Skudai'
            ],
            [
                'state_id' => 1,
                'name' => 'Sungai Mati'
            ],
            [
                'state_id' => 1,
                'name' => 'Tampoi'
            ],
            [
                'state_id' => 1,
                'name' => 'Tangkak'
            ],
            [
                'state_id' => 1,
                'name' => 'Ulu Tiram'
            ],
            [
                'state_id' => 1,
                'name' => 'Yong Peng'
            ],
            [
                'state_id' => 2,
                'name' => 'Alor Setar'
            ],
            [
                'state_id' => 2,
                'name' => 'Baling'
            ],
            [
                'state_id' => 2,
                'name' => 'Bandar Baharu'
            ],
            [
                'state_id' => 2,
                'name' => 'Bedong'
            ],
            [
                'state_id' => 2,
                'name' => 'Bukit Kayu Hitam'
            ],
            [
                'state_id' => 2,
                'name' => 'Guar Chempedak'
            ],
            [
                'state_id' => 2,
                'name' => 'Gurun'
            ],
            [
                'state_id' => 2,
                'name' => 'Jitra'
            ],
            [
                'state_id' => 2,
                'name' => 'Karangan'
            ],
            [
                'state_id' => 2,
                'name' => 'Kodiang'
            ],
            [
                'state_id' => 2,
                'name' => 'Kota Sarang Semut'
            ],
            [
                'state_id' => 2,
                'name' => 'Kota Setar'
            ],
            [
                'state_id' => 2,
                'name' => 'Kuala Kedah'
            ],
            [
                'state_id' => 2,
                'name' => 'Kuala Ketil'
            ],
            [
                'state_id' => 2,
                'name' => 'Kuala Muda'
            ],
            [
                'state_id' => 2,
                'name' => 'Kuala Nerang'
            ],
            [
                'state_id' => 2,
                'name' => 'Kubang Pasu'
            ],
            [
                'state_id' => 2,
                'name' => 'Kulim'
            ],
            [
                'state_id' => 2,
                'name' => 'Kupang'
            ],
            [
                'state_id' => 2,
                'name' => 'Langgar'
            ],
            [
                'state_id' => 2,
                'name' => 'Lunas'
            ],
            [
                'state_id' => 2,
                'name' => 'Merbok'
            ],
            [
                'state_id' => 2,
                'name' => 'Padang Serai'
            ],
            [
                'state_id' => 2,
                'name' => 'Padang Terap'
            ],
            [
                'state_id' => 2,
                'name' => 'Pendang'
            ],
            [
                'state_id' => 2,
                'name' => 'Pokok Sena'
            ],
            [
                'state_id' => 2,
                'name' => 'Pulau Langkawi'
            ],
            [
                'state_id' => 2,
                'name' => 'Sik'
            ],
            [
                'state_id' => 2,
                'name' => 'Simpang Empat'
            ],
            [
                'state_id' => 2,
                'name' => 'Sungai Petani'
            ],
            [
                'state_id' => 2,
                'name' => 'University Utara'
            ],
            [
                'state_id' => 2,
                'name' => 'Yan'
            ],
            [
                'state_id' => 3,
                'name' => 'Ayer Lanas'
            ],
            [
                'state_id' => 3,
                'name' => 'Bachok'
            ],
            [
                'state_id' => 3,
                'name' => 'Cherang Ruku'
            ],
            [
                'state_id' => 3,
                'name' => 'Dabong'
            ],
            [
                'state_id' => 3,
                'name' => 'Gua Musang'
            ],
            [
                'state_id' => 3,
                'name' => 'Jeli'
            ],
            [
                'state_id' => 3,
                'name' => 'Kem Desa Pahlawan'
            ],
            [
                'state_id' => 3,
                'name' => 'Ketereh'
            ],
            [
                'state_id' => 3,
                'name' => 'Kota Bharu'
            ],
            [
                'state_id' => 3,
                'name' => 'Kuala Balah'
            ],
            [
                'state_id' => 3,
                'name' => 'Kuala Kerai'
            ],
            [
                'state_id' => 3,
                'name' => 'Kuala Krai'
            ],
            [
                'state_id' => 3,
                'name' => 'Machang'
            ],
            [
                'state_id' => 3,
                'name' => 'Melor'
            ],
            [
                'state_id' => 3,
                'name' => 'Pasir Mas'
            ],
            [
                'state_id' => 3,
                'name' => 'Pasir Puteh'
            ],
            [
                'state_id' => 3,
                'name' => 'Pulai Chondong'
            ],
            [
                'state_id' => 3,
                'name' => 'Rantau Panjang'
            ],
            [
                'state_id' => 3,
                'name' => 'Selising'
            ],
            [
                'state_id' => 3,
                'name' => 'Tanah Merah'
            ],
            [
                'state_id' => 3,
                'name' => 'Tawang'
            ],
            [
                'state_id' => 3,
                'name' => 'Temangan'
            ],
            [
                'state_id' => 3,
                'name' => 'Tumpat'
            ],
            [
                'state_id' => 3,
                'name' => 'Wakaf Baru'
            ],
            [
                'state_id' => 4,
                'name' => 'Alor Gajah'
            ],
            [
                'state_id' => 4,
                'name' => 'Asahan'
            ],
            [
                'state_id' => 4,
                'name' => 'Ayer Keroh'
            ],
            [
                'state_id' => 4,
                'name' => 'Bandar Hilir'
            ],
            [
                'state_id' => 4,
                'name' => 'Batu Berendam'
            ],
            [
                'state_id' => 4,
                'name' => 'Bemban'
            ],
            [
                'state_id' => 4,
                'name' => 'Bukit Beruang'
            ],
            [
                'state_id' => 4,
                'name' => 'Durian Tunggal'
            ],
            [
                'state_id' => 4,
                'name' => 'Jasin'
            ],
            [
                'state_id' => 4,
                'name' => 'Kuala Linggi'
            ],
            [
                'state_id' => 4,
                'name' => 'Kuala Sungai Baru'
            ],
            [
                'state_id' => 4,
                'name' => 'Lubok China'
            ],
            [
                'state_id' => 4,
                'name' => 'Masjid Tanah'
            ],
            [
                'state_id' => 4,
                'name' => 'Melaka Tengah'
            ],
            [
                'state_id' => 4,
                'name' => 'Merlimau'
            ],
            [
                'state_id' => 4,
                'name' => 'Selandar'
            ],
            [
                'state_id' => 4,
                'name' => 'Sungai Rambai'
            ],
            [
                'state_id' => 4,
                'name' => 'Sungai Udang'
            ],
            [
                'state_id' => 4,
                'name' => 'Tanjong Kling'
            ],
            [
                'state_id' => 4,
                'name' => 'Ujong Pasir'
            ],
            [
                'state_id' => 5,
                'name' => 'Bahau'
            ],
            [
                'state_id' => 5,
                'name' => 'Bandar Baru Serting'
            ],
            [
                'state_id' => 5,
                'name' => 'Batang Melaka'
            ],
            [
                'state_id' => 5,
                'name' => 'Batu Kikir'
            ],
            [
                'state_id' => 5,
                'name' => 'Gemas'
            ],
            [
                'state_id' => 5,
                'name' => 'Gemencheh'
            ],
            [
                'state_id' => 5,
                'name' => 'Jelebu'
            ],
            [
                'state_id' => 5,
                'name' => 'Jempol'
            ],
            [
                'state_id' => 5,
                'name' => 'Johol'
            ],
            [
                'state_id' => 5,
                'name' => 'Juasseh'
            ],
            [
                'state_id' => 5,
                'name' => 'Kota'
            ],
            [
                'state_id' => 5,
                'name' => 'Kuala Klawang'
            ],
            [
                'state_id' => 5,
                'name' => 'Kuala Pilah'
            ],
            [
                'state_id' => 5,
                'name' => 'Labu'
            ],
            [
                'state_id' => 5,
                'name' => 'Lenggeng'
            ],
            [
                'state_id' => 5,
                'name' => 'Linggi'
            ],
            [
                'state_id' => 5,
                'name' => 'Mantin'
            ],
            [
                'state_id' => 5,
                'name' => 'Nilai'
            ],
            [
                'state_id' => 5,
                'name' => 'Pasir Panjang'
            ],
            [
                'state_id' => 5,
                'name' => 'Pedas'
            ],
            [
                'state_id' => 5,
                'name' => 'Port Dickson'
            ],
            [
                'state_id' => 5,
                'name' => 'Rantau'
            ],
            [
                'state_id' => 5,
                'name' => 'Rembau'
            ],
            [
                'state_id' => 5,
                'name' => 'Senawang'
            ],
            [
                'state_id' => 5,
                'name' => 'Seremban'
            ],
            [
                'state_id' => 5,
                'name' => 'Si Rusa'
            ],
            [
                'state_id' => 5,
                'name' => 'Siliau'
            ],
            [
                'state_id' => 5,
                'name' => 'Simpang Durian'
            ],
            [
                'state_id' => 5,
                'name' => 'Simpang Pertang'
            ],
            [
                'state_id' => 5,
                'name' => 'Sri Menanti'
            ],
            [
                'state_id' => 5,
                'name' => 'Tampin'
            ],
            [
                'state_id' => 5,
                'name' => 'Tanjong Ipoh'
            ],
            [
                'state_id' => 6,
                'name' => 'Balok'
            ],
            [
                'state_id' => 6,
                'name' => 'Bandar Pusat Jengka'
            ],
            [
                'state_id' => 6,
                'name' => 'Bandar Tun Abdul Razak'
            ],
            [
                'state_id' => 6,
                'name' => 'Benta'
            ],
            [
                'state_id' => 6,
                'name' => 'Bentong'
            ],
            [
                'state_id' => 6,
                'name' => 'Bera'
            ],
            [
                'state_id' => 6,
                'name' => 'Brinchang'
            ],
            [
                'state_id' => 6,
                'name' => 'Bukit Fraser'
            ],
            [
                'state_id' => 6,
                'name' => 'Cameron Highlands'
            ],
            [
                'state_id' => 6,
                'name' => 'Chenor'
            ],
            [
                'state_id' => 6,
                'name' => 'Daerah Rompin'
            ],
            [
                'state_id' => 6,
                'name' => 'Damak'
            ],
            [
                'state_id' => 6,
                'name' => 'Dong'
            ],
            [
                'state_id' => 6,
                'name' => 'Genting Highlands'
            ],
            [
                'state_id' => 6,
                'name' => 'Jerantut'
            ],
            [
                'state_id' => 6,
                'name' => 'Karak'
            ],
            [
                'state_id' => 6,
                'name' => 'Kuala Lipis'
            ],
            [
                'state_id' => 6,
                'name' => 'Kuala Rompin'
            ],
            [
                'state_id' => 6,
                'name' => 'Kuantan'
            ],
            [
                'state_id' => 6,
                'name' => 'Lanchang'
            ],
            [
                'state_id' => 6,
                'name' => 'Lurah Bilut'
            ],
            [
                'state_id' => 6,
                'name' => 'Maran'
            ],
            [
                'state_id' => 6,
                'name' => 'Mengkarak'
            ],
            [
                'state_id' => 6,
                'name' => 'Mentakab'
            ],
            [
                'state_id' => 6,
                'name' => 'Muadzam Shah'
            ],
            [
                'state_id' => 6,
                'name' => 'Padang Tengku'
            ],
            [
                'state_id' => 6,
                'name' => 'Pekan'
            ],
            [
                'state_id' => 6,
                'name' => 'Raub'
            ],
            [
                'state_id' => 6,
                'name' => 'Ringlet'
            ],
            [
                'state_id' => 6,
                'name' => 'Rompin'
            ],
            [
                'state_id' => 6,
                'name' => 'Sega'
            ],
            [
                'state_id' => 6,
                'name' => 'Sungai Koyan'
            ],
            [
                'state_id' => 6,
                'name' => 'Sungai Lembing'
            ],
            [
                'state_id' => 6,
                'name' => 'Sungai Ruan'
            ],
            [
                'state_id' => 6,
                'name' => 'Tanah Rata'
            ],
            [
                'state_id' => 6,
                'name' => 'Temerloh'
            ],
            [
                'state_id' => 6,
                'name' => 'Triang'
            ],
            [
                'state_id' => 7,
                'name' => 'Air Tawar'
            ],
            [
                'state_id' => 7,
                'name' => 'Alma'
            ],
            [
                'state_id' => 7,
                'name' => 'Ayer Itam'
            ],
            [
                'state_id' => 7,
                'name' => 'Bagan Ajam'
            ],
            [
                'state_id' => 7,
                'name' => 'Bagan Jermal'
            ],
            [
                'state_id' => 7,
                'name' => 'Bagan Lalang'
            ],
            [
                'state_id' => 7,
                'name' => 'Balik Pulau'
            ],
            [
                'state_id' => 7,
                'name' => 'Bandar Perda'
            ],
            [
                'state_id' => 7,
                'name' => 'Barat Daya'
            ],
            [
                'state_id' => 7,
                'name' => 'Batu Ferringhi'
            ],
            [
                'state_id' => 7,
                'name' => 'Batu Kawan'
            ],
            [
                'state_id' => 7,
                'name' => 'Batu Maung'
            ],
            [
                'state_id' => 7,
                'name' => 'Batu Uban'
            ],
            [
                'state_id' => 7,
                'name' => 'Bayan Baru'
            ],
            [
                'state_id' => 7,
                'name' => 'Bayan Lepas'
            ],
            [
                'state_id' => 7,
                'name' => 'Berapit'
            ],
            [
                'state_id' => 7,
                'name' => 'Bertam'
            ],
            [
                'state_id' => 7,
                'name' => 'Bukit Dumbar'
            ],
            [
                'state_id' => 7,
                'name' => 'Bukit Jambul'
            ],
            [
                'state_id' => 7,
                'name' => 'Bukit Mertajam'
            ],
            [
                'state_id' => 7,
                'name' => 'Bukit Minyak'
            ],
            [
                'state_id' => 7,
                'name' => 'Bukit Tambun'
            ],
            [
                'state_id' => 7,
                'name' => 'Bukit Tangah'
            ],
            [
                'state_id' => 7,
                'name' => 'Butterworth'
            ],
            [
                'state_id' => 7,
                'name' => 'Gelugor'
            ],
            [
                'state_id' => 7,
                'name' => 'Georgetown'
            ],
            [
                'state_id' => 7,
                'name' => 'Gertak Sangul'
            ],
            [
                'state_id' => 7,
                'name' => 'Greenlane'
            ],
            [
                'state_id' => 7,
                'name' => 'Jawi'
            ],
            [
                'state_id' => 7,
                'name' => 'Jelutong'
            ],
            [
                'state_id' => 7,
                'name' => 'Juru'
            ],
            [
                'state_id' => 7,
                'name' => 'Kepala Batas'
            ],
            [
                'state_id' => 7,
                'name' => 'Kubang Semang'
            ],
            [
                'state_id' => 7,
                'name' => 'Mak Mandin'
            ],
            [
                'state_id' => 7,
                'name' => 'Minden Heights'
            ],
            [
                'state_id' => 7,
                'name' => 'Nibong Tebal'
            ],
            [
                'state_id' => 7,
                'name' => 'Pauh Jaya'
            ],
            [
                'state_id' => 7,
                'name' => 'Paya Terubong'
            ],
            [
                'state_id' => 7,
                'name' => 'Penaga'
            ],
            [
                'state_id' => 7,
                'name' => 'Penang Hill'
            ],
            [
                'state_id' => 7,
                'name' => 'Penanti'
            ],
            [
                'state_id' => 7,
                'name' => 'Perai'
            ],
            [
                'state_id' => 7,
                'name' => 'Permatang Kuching'
            ],
            [
                'state_id' => 7,
                'name' => 'Permatang Pauh'
            ],
            [
                'state_id' => 7,
                'name' => 'Permatang Tinggi'
            ],
            [
                'state_id' => 7,
                'name' => 'Persiaran Gurney'
            ],
            [
                'state_id' => 7,
                'name' => 'Prai'
            ],
            [
                'state_id' => 7,
                'name' => 'Pulau Betong'
            ],
            [
                'state_id' => 7,
                'name' => 'Pulau Tikus'
            ],
            [
                'state_id' => 7,
                'name' => 'Raja Uda'
            ],
            [
                'state_id' => 7,
                'name' => 'Relau'
            ],
            [
                'state_id' => 7,
                'name' => 'Scotland'
            ],
            [
                'state_id' => 7,
                'name' => 'Seberang Jaya'
            ],
            [
                'state_id' => 7,
                'name' => 'Seberang Perai'
            ],
            [
                'state_id' => 7,
                'name' => 'Simpang Ampat'
            ],
            [
                'state_id' => 7,
                'name' => 'Sungai Ara'
            ],
            [
                'state_id' => 7,
                'name' => 'Sungai Bakap'
            ],
            [
                'state_id' => 7,
                'name' => 'Sungai Dua'
            ],
            [
                'state_id' => 7,
                'name' => 'Sungai Jawi '
            ],
            [
                'state_id' => 7,
                'name' => 'Sungai Nibong'
            ],
            [
                'state_id' => 7,
                'name' => 'Sungai Pinang'
            ],
            [
                'state_id' => 7,
                'name' => 'Tanjong Tokong'
            ],
            [
                'state_id' => 7,
                'name' => 'Tanjung Bungah'
            ],
            [
                'state_id' => 7,
                'name' => 'Tasek Gelugor'
            ],
            [
                'state_id' => 7,
                'name' => 'Teluk Bahang'
            ],
            [
                'state_id' => 7,
                'name' => 'Teluk Kumbar'
            ],
            [
                'state_id' => 7,
                'name' => 'USM'
            ],
            [
                'state_id' => 7,
                'name' => 'Valdor'
            ],
            [
                'state_id' => 8,
                'name' => 'Ayer Tawar'
            ],
            [
                'state_id' => 8,
                'name' => 'Bagan Datoh'
            ],
            [
                'state_id' => 8,
                'name' => 'Bagan Serai'
            ],
            [
                'state_id' => 8,
                'name' => 'Batang Padang'
            ],
            [
                'state_id' => 8,
                'name' => 'Batu Gajah'
            ],
            [
                'state_id' => 8,
                'name' => 'Batu Kurau'
            ],
            [
                'state_id' => 8,
                'name' => 'Behrang Stesen'
            ],
            [
                'state_id' => 8,
                'name' => 'Beruas'
            ],
            [
                'state_id' => 8,
                'name' => 'Bidor'
            ],
            [
                'state_id' => 8,
                'name' => 'Bota'
            ],
            [
                'state_id' => 8,
                'name' => 'Changkat Jering'
            ],
            [
                'state_id' => 8,
                'name' => 'Changkat Keruing'
            ],
            [
                'state_id' => 8,
                'name' => 'Chemor'
            ],
            [
                'state_id' => 8,
                'name' => 'Chenderiang'
            ],
            [
                'state_id' => 8,
                'name' => 'Chenderong Balai'
            ],
            [
                'state_id' => 8,
                'name' => 'Chikus'
            ],
            [
                'state_id' => 8,
                'name' => 'Enggor'
            ],
            [
                'state_id' => 8,
                'name' => 'Gerik'
            ],
            [
                'state_id' => 8,
                'name' => 'Gopeng'
            ],
            [
                'state_id' => 8,
                'name' => 'Hilir Perak'
            ],
            [
                'state_id' => 8,
                'name' => 'Hulu Perak'
            ],
            [
                'state_id' => 8,
                'name' => 'Hutan Melintang'
            ],
            [
                'state_id' => 8,
                'name' => 'Intan'
            ],
            [
                'state_id' => 8,
                'name' => 'Ipoh'
            ],
            [
                'state_id' => 8,
                'name' => 'Jeram'
            ],
            [
                'state_id' => 8,
                'name' => 'Kampar'
            ],
            [
                'state_id' => 8,
                'name' => 'Kampong Gajah'
            ],
            [
                'state_id' => 8,
                'name' => 'Kampong Kepayang'
            ],
            [
                'state_id' => 8,
                'name' => 'Kamunting'
            ],
            [
                'state_id' => 8,
                'name' => 'Kerian'
            ],
            [
                'state_id' => 8,
                'name' => 'Kinta'
            ],
            [
                'state_id' => 8,
                'name' => 'Kuala Kangsar'
            ],
            [
                'state_id' => 8,
                'name' => 'Kuala Kurau'
            ],
            [
                'state_id' => 8,
                'name' => 'Kuala Sepatang'
            ],
            [
                'state_id' => 8,
                'name' => 'Lahat'
            ],
            [
                'state_id' => 8,
                'name' => 'Lambor Kanan'
            ],
            [
                'state_id' => 8,
                'name' => 'Langkap'
            ],
            [
                'state_id' => 8,
                'name' => 'Larut'
            ],
            [
                'state_id' => 8,
                'name' => 'Lenggong'
            ],
            [
                'state_id' => 8,
                'name' => 'Lumut'
            ],
            [
                'state_id' => 8,
                'name' => 'Malim Nawar'
            ],
            [
                'state_id' => 8,
                'name' => 'Mambang Diawan'
            ],
            [
                'state_id' => 8,
                'name' => 'Manjung'
            ],
            [
                'state_id' => 8,
                'name' => 'Manong'
            ],
            [
                'state_id' => 8,
                'name' => 'Matang'
            ],
            [
                'state_id' => 8,
                'name' => 'Menglembu'
            ],
            [
                'state_id' => 8,
                'name' => 'Padang Rengas'
            ],
            [
                'state_id' => 8,
                'name' => 'Pangkor'
            ],
            [
                'state_id' => 8,
                'name' => 'Pantai Remis'
            ],
            [
                'state_id' => 8,
                'name' => 'Parit'
            ],
            [
                'state_id' => 8,
                'name' => 'Parit Buntar'
            ],
            [
                'state_id' => 8,
                'name' => 'Pengkalan Hulu'
            ],
            [
                'state_id' => 8,
                'name' => 'Perak Tengah'
            ],
            [
                'state_id' => 8,
                'name' => 'Pusing'
            ],
            [
                'state_id' => 8,
                'name' => 'Sauk'
            ],
            [
                'state_id' => 8,
                'name' => 'Selama'
            ],
            [
                'state_id' => 8,
                'name' => 'Selekoh'
            ],
            [
                'state_id' => 8,
                'name' => 'Selinsing'
            ],
            [
                'state_id' => 8,
                'name' => 'Semanggol'
            ],
            [
                'state_id' => 8,
                'name' => 'Seri Manjong'
            ],
            [
                'state_id' => 8,
                'name' => 'Seri Iskandar'
            ],
            [
                'state_id' => 8,
                'name' => 'Simpang'
            ],
            [
                'state_id' => 8,
                'name' => 'Sitiawan'
            ],
            [
                'state_id' => 8,
                'name' => 'Slim River'
            ],
            [
                'state_id' => 8,
                'name' => 'Sungai Siput'
            ],
            [
                'state_id' => 8,
                'name' => 'Sungai Sumun'
            ],
            [
                'state_id' => 8,
                'name' => 'Sungkai'
            ],
            [
                'state_id' => 8,
                'name' => 'Taiping'
            ],
            [
                'state_id' => 8,
                'name' => 'Tanjong Piandang'
            ],
            [
                'state_id' => 8,
                'name' => 'Tanjong Rambutan'
            ],
            [
                'state_id' => 8,
                'name' => 'Tanjong Tualang'
            ],
            [
                'state_id' => 8,
                'name' => 'Tanjung Malim'
            ],
            [
                'state_id' => 8,
                'name' => 'Tapah'
            ],
            [
                'state_id' => 8,
                'name' => 'Teluk Intan'
            ],
            [
                'state_id' => 8,
                'name' => 'Temoh'
            ],
            [
                'state_id' => 8,
                'name' => 'TLDM Lumut'
            ],
            [
                'state_id' => 8,
                'name' => 'Trolak'
            ],
            [
                'state_id' => 8,
                'name' => 'Trong'
            ],
            [
                'state_id' => 8,
                'name' => 'Tronoh'
            ],
            [
                'state_id' => 8,
                'name' => 'Ulu Bernam'
            ],
            [
                'state_id' => 8,
                'name' => 'Ulu Kinta'
            ],
            [
                'state_id' => 9,
                'name' => 'Arau'
            ],
            [
                'state_id' => 9,
                'name' => 'Kaki Bukit'
            ],
            [
                'state_id' => 9,
                'name' => 'Kangar'
            ],
            [
                'state_id' => 9,
                'name' => 'Kuala Perlis'
            ],
            [
                'state_id' => 9,
                'name' => 'Padang Besar'
            ],
            [
                'state_id' => 9,
                'name' => 'Pauh'
            ],
            [
                'state_id' => 10,
                'name' => 'Beaufort'
            ],
            [
                'state_id' => 10,
                'name' => 'Beluran'
            ],
            [
                'state_id' => 10,
                'name' => 'Bongawan'
            ],
            [
                'state_id' => 10,
                'name' => 'Keningau'
            ],
            [
                'state_id' => 10,
                'name' => 'Kota Belud'
            ],
            [
                'state_id' => 10,
                'name' => 'Kota Kinabalu'
            ],
            [
                'state_id' => 10,
                'name' => 'Kota Kinabatangan'
            ],
            [
                'state_id' => 10,
                'name' => 'Kota Marudu'
            ],
            [
                'state_id' => 10,
                'name' => 'Kuala Penyu'
            ],
            [
                'state_id' => 10,
                'name' => 'Kudat'
            ],
            [
                'state_id' => 10,
                'name' => 'Kunak'
            ],
            [
                'state_id' => 10,
                'name' => 'Lahad Datu'
            ],
            [
                'state_id' => 10,
                'name' => 'Likas'
            ],
            [
                'state_id' => 10,
                'name' => 'Membakut'
            ],
            [
                'state_id' => 10,
                'name' => 'Menumbok'
            ],
            [
                'state_id' => 10,
                'name' => 'Nabawan'
            ],
            [
                'state_id' => 10,
                'name' => 'Pamol'
            ],
            [
                'state_id' => 10,
                'name' => 'Papar'
            ],
            [
                'state_id' => 10,
                'name' => 'Penampang'
            ],
            [
                'state_id' => 10,
                'name' => 'Pitas'
            ],
            [
                'state_id' => 10,
                'name' => 'Putatan'
            ],
            [
                'state_id' => 10,
                'name' => 'Ranau'
            ],
            [
                'state_id' => 10,
                'name' => 'Sandakan'
            ],
            [
                'state_id' => 10,
                'name' => 'Semporna'
            ],
            [
                'state_id' => 10,
                'name' => 'Sipitang'
            ],
            [
                'state_id' => 10,
                'name' => 'Tambunan'
            ],
            [
                'state_id' => 10,
                'name' => 'Tamparuli'
            ],
            [
                'state_id' => 10,
                'name' => 'Tawau'
            ],
            [
                'state_id' => 10,
                'name' => 'Tenom'
            ],
            [
                'state_id' => 10,
                'name' => 'Tongod'
            ],
            [
                'state_id' => 10,
                'name' => 'Tuaran'
            ],
            [
                'state_id' => 11,
                'name' => 'Asajaya'
            ],
            [
                'state_id' => 11,
                'name' => 'Balingian'
            ],
            [
                'state_id' => 11,
                'name' => 'Baram'
            ],
            [
                'state_id' => 11,
                'name' => 'Bau'
            ],
            [
                'state_id' => 11,
                'name' => 'Bekenu'
            ],
            [
                'state_id' => 11,
                'name' => 'Belaga'
            ],
            [
                'state_id' => 11,
                'name' => 'Belawai'
            ],
            [
                'state_id' => 11,
                'name' => 'Betong'
            ],
            [
                'state_id' => 11,
                'name' => 'Bintagor'
            ],
            [
                'state_id' => 11,
                'name' => 'Bintulu'
            ],
            [
                'state_id' => 11,
                'name' => 'Dalat'
            ],
            [
                'state_id' => 11,
                'name' => 'Daro'
            ],
            [
                'state_id' => 11,
                'name' => 'Debak'
            ],
            [
                'state_id' => 11,
                'name' => 'Engkilili'
            ],
            [
                'state_id' => 11,
                'name' => 'Julau'
            ],
            [
                'state_id' => 11,
                'name' => 'Kabong'
            ],
            [
                'state_id' => 11,
                'name' => 'Kanowit'
            ],
            [
                'state_id' => 11,
                'name' => 'Kapit'
            ],
            [
                'state_id' => 11,
                'name' => 'Kota Samarahan'
            ],
            [
                'state_id' => 11,
                'name' => 'Kuching'
            ],
            [
                'state_id' => 11,
                'name' => 'Lawas'
            ],
            [
                'state_id' => 11,
                'name' => 'Limbang'
            ],
            [
                'state_id' => 11,
                'name' => 'Lingga'
            ],
            [
                'state_id' => 11,
                'name' => 'Long Lama'
            ],
            [
                'state_id' => 11,
                'name' => 'Lubok Antu'
            ],
            [
                'state_id' => 11,
                'name' => 'Lundu'
            ],
            [
                'state_id' => 11,
                'name' => 'Lutong'
            ],
            [
                'state_id' => 11,
                'name' => 'Maradong'
            ],
            [
                'state_id' => 11,
                'name' => 'Marudi'
            ],
            [
                'state_id' => 11,
                'name' => 'Matu'
            ],
            [
                'state_id' => 11,
                'name' => 'Miri'
            ],
            [
                'state_id' => 11,
                'name' => 'Mukah'
            ],
            [
                'state_id' => 11,
                'name' => 'Nanga Medamit'
            ],
            [
                'state_id' => 11,
                'name' => 'Niah'
            ],
            [
                'state_id' => 11,
                'name' => 'Pakan'
            ],
            [
                'state_id' => 11,
                'name' => 'Pusa'
            ],
            [
                'state_id' => 11,
                'name' => 'Roban'
            ],
            [
                'state_id' => 11,
                'name' => 'Saratok'
            ],
            [
                'state_id' => 11,
                'name' => 'Sarikei'
            ],
            [
                'state_id' => 11,
                'name' => 'Sebauh'
            ],
            [
                'state_id' => 11,
                'name' => 'Sebuyau'
            ],
            [
                'state_id' => 11,
                'name' => 'Selangau'
            ],
            [
                'state_id' => 11,
                'name' => 'Serian'
            ],
            [
                'state_id' => 11,
                'name' => 'Sibu'
            ],
            [
                'state_id' => 11,
                'name' => 'Simunjan'
            ],
            [
                'state_id' => 11,
                'name' => 'Song'
            ],
            [
                'state_id' => 11,
                'name' => 'Spaoh'
            ],
            [
                'state_id' => 11,
                'name' => 'Sri Aman'
            ],
            [
                'state_id' => 11,
                'name' => 'Sundar'
            ],
            [
                'state_id' => 11,
                'name' => 'Tanjung Kidurong'
            ],
            [
                'state_id' => 11,
                'name' => 'Tatau'
            ],
            [
                'state_id' => 12,
                'name' => 'Alam Impian'
            ],
            [
                'state_id' => 12,
                'name' => 'Aman Perdana'
            ],
            [
                'state_id' => 12,
                'name' => 'Ambang Botanic'
            ],
            [
                'state_id' => 12,
                'name' => 'Ampang'
            ],
            [
                'state_id' => 12,
                'name' => 'Ara Damansara'
            ],
            [
                'state_id' => 12,
                'name' => 'Balakong'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Botanic'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Bukit Raja'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Bukit Tinggi'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Kinrara'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Puteri Klang'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Puteri Puchong'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Saujana Putra'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Sungai Long'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Sunway'
            ],
            [
                'state_id' => 12,
                'name' => 'Bandar Utama'
            ],
            [
                'state_id' => 12,
                'name' => 'Bangi'
            ],
            [
                'state_id' => 12,
                'name' => 'Banting'
            ],
            [
                'state_id' => 12,
                'name' => 'Batang Berjuntai'
            ],
            [
                'state_id' => 12,
                'name' => 'Batang Kali'
            ],
            [
                'state_id' => 12,
                'name' => 'Batu Arang'
            ],
            [
                'state_id' => 12,
                'name' => 'Batu Caves'
            ],
            [
                'state_id' => 12,
                'name' => 'Beranang'
            ],
            [
                'state_id' => 12,
                'name' => 'Bukit Antarabangsa'
            ],
            [
                'state_id' => 12,
                'name' => 'Bukit Jelutong'
            ],
            [
                'state_id' => 12,
                'name' => 'Bukit Rahman Putra'
            ],
            [
                'state_id' => 12,
                'name' => 'Bukit Rotan'
            ],
            [
                'state_id' => 12,
                'name' => 'Bukit Subang'
            ],
            [
                'state_id' => 12,
                'name' => 'Contry Heights'
            ],
            [
                'state_id' => 12,
                'name' => 'Cyberjaya'
            ],
            [
                'state_id' => 12,
                'name' => 'Damansara Damai'
            ],
            [
                'state_id' => 12,
                'name' => 'Damansara Intan'
            ],
            [
                'state_id' => 12,
                'name' => 'Damansara Jaya'
            ],
            [
                'state_id' => 12,
                'name' => 'Damansara Kim'
            ],
            [
                'state_id' => 12,
                'name' => 'Damansara Perdana'
            ],
            [
                'state_id' => 12,
                'name' => 'Damansara Utama'
            ],
            [
                'state_id' => 12,
                'name' => 'Denai Alam'
            ],
            [
                'state_id' => 12,
                'name' => 'Dengkil'
            ],
            [
                'state_id' => 12,
                'name' => 'Glenmarie'
            ],
            [
                'state_id' => 12,
                'name' => 'Gombak'
            ],
            [
                'state_id' => 12,
                'name' => 'Hulu Langat'
            ],
            [
                'state_id' => 12,
                'name' => 'Hulu Selangor'
            ],
            [
                'state_id' => 12,
                'name' => 'Hulu Selangor'
            ],
            [
                'state_id' => 12,
                'name' => 'Kajang'
            ],
            [
                'state_id' => 12,
                'name' => 'Kapar'
            ],
            [
                'state_id' => 12,
                'name' => 'Kayu Ara'
            ],
            [
                'state_id' => 12,
                'name' => 'Kelana Jaya'
            ],
            [
                'state_id' => 12,
                'name' => 'Kerling'
            ],
            [
                'state_id' => 12,
                'name' => 'Klang'
            ],
            [
                'state_id' => 12,
                'name' => 'Kota Damansara'
            ],
            [
                'state_id' => 12,
                'name' => 'Kota Emerald'
            ],
            [
                'state_id' => 12,
                'name' => 'Kota Kemuning'
            ],
            [
                'state_id' => 12,
                'name' => 'Kuala Kubu Baru'
            ],
            [
                'state_id' => 12,
                'name' => 'Kuala Langat'
            ],
            [
                'state_id' => 12,
                'name' => 'Kuala Selangor'
            ],
            [
                'state_id' => 12,
                'name' => 'Kuang'
            ],
            [
                'state_id' => 12,
                'name' => 'Mutiara Damansara'
            ],
            [
                'state_id' => 12,
                'name' => 'Petaling Jaya'
            ],
            [
                'state_id' => 12,
                'name' => 'Port Klang'
            ],
            [
                'state_id' => 12,
                'name' => 'Puchong'
            ],
            [
                'state_id' => 12,
                'name' => 'Puchong South'
            ],
            [
                'state_id' => 12,
                'name' => 'Pulau Carey'
            ],
            [
                'state_id' => 12,
                'name' => 'Pulau Indah (Pulau Lumut)'
            ],
            [
                'state_id' => 12,
                'name' => 'Pulau Ketam'
            ],
            [
                'state_id' => 12,
                'name' => 'Puncak Jalil'
            ],
            [
                'state_id' => 12,
                'name' => 'Putra Heights'
            ],
            [
                'state_id' => 12,
                'name' => 'Rasa'
            ],
            [
                'state_id' => 12,
                'name' => 'Rawang'
            ],
            [
                'state_id' => 12,
                'name' => 'Sabak Bernam'
            ],
            [
                'state_id' => 12,
                'name' => 'Saujana'
            ],
            [
                'state_id' => 12,
                'name' => 'Sekinchan'
            ],
            [
                'state_id' => 12,
                'name' => 'Selayang'
            ],
            [
                'state_id' => 12,
                'name' => 'Semenyih'
            ],
            [
                'state_id' => 12,
                'name' => 'Sepang'
            ],
            [
                'state_id' => 12,
                'name' => 'Serdang'
            ],
            [
                'state_id' => 12,
                'name' => 'Serendah'
            ],
            [
                'state_id' => 12,
                'name' => 'Seri Kembangan'
            ],
            [
                'state_id' => 12,
                'name' => 'Setia Alam'
            ],
            [
                'state_id' => 12,
                'name' => 'Setia Eco Park'
            ],
            [
                'state_id' => 12,
                'name' => 'Shah Alam'
            ],
            [
                'state_id' => 12,
                'name' => 'SierraMas'
            ],
            [
                'state_id' => 12,
                'name' => 'SS2'
            ],
            [
                'state_id' => 12,
                'name' => 'Subang Bestari'
            ],
            [
                'state_id' => 12,
                'name' => 'Subang Heights'
            ],
            [
                'state_id' => 12,
                'name' => 'Subang Jaya'
            ],
            [
                'state_id' => 12,
                'name' => 'Sungai Ayer Tawar'
            ],
            [
                'state_id' => 12,
                'name' => 'Sungai Besar'
            ],
            [
                'state_id' => 12,
                'name' => 'Sungai Buloh'
            ],
            [
                'state_id' => 12,
                'name' => 'Sungai Pelek'
            ],
            [
                'state_id' => 12,
                'name' => 'Taman TTDI Jaya'
            ],
            [
                'state_id' => 12,
                'name' => 'Tanjong Karang'
            ],
            [
                'state_id' => 12,
                'name' => 'Tanjong Sepat'
            ],
            [
                'state_id' => 12,
                'name' => 'Telok Panglima Garang'
            ],
            [
                'state_id' => 12,
                'name' => 'Tropicana'
            ],
            [
                'state_id' => 12,
                'name' => 'USJ'
            ],
            [
                'state_id' => 12,
                'name' => 'USJ Heights'
            ],
            [
                'state_id' => 12,
                'name' => 'Valencia'
            ],
            [
                'state_id' => 13,
                'name' => 'Besut'
            ],
            [
                'state_id' => 13,
                'name' => 'Dungun'
            ],
            [
                'state_id' => 13,
                'name' => 'Hulu Terengganu'
            ],
            [
                'state_id' => 13,
                'name' => 'Kemaman'
            ],
            [
                'state_id' => 13,
                'name' => 'Kuala Terengganu'
            ],
            [
                'state_id' => 13,
                'name' => 'Marang'
            ],
            [
                'state_id' => 13,
                'name' => 'Setiu'
            ],
            [
                'state_id' => 14,
                'name' => 'Ampang Hilir'
            ],
            [
                'state_id' => 14,
                'name' => 'Bandar Damai Perdana'
            ],
            [
                'state_id' => 14,
                'name' => 'Bandar Menjalara'
            ],
            [
                'state_id' => 14,
                'name' => 'Bandar Tasik Selatan'
            ],
            [
                'state_id' => 14,
                'name' => 'Bangsar'
            ],
            [
                'state_id' => 14,
                'name' => 'Bangsar South'
            ],
            [
                'state_id' => 14,
                'name' => 'Batu'
            ],
            [
                'state_id' => 14,
                'name' => 'Brickfields'
            ],
            [
                'state_id' => 14,
                'name' => 'Bukit Bintang'
            ],
            [
                'state_id' => 14,
                'name' => 'Bukit Jalil'
            ],
            [
                'state_id' => 14,
                'name' => 'Bukit Ledang'
            ],
            [
                'state_id' => 14,
                'name' => 'Bukit Persekutuan'
            ],
            [
                'state_id' => 14,
                'name' => 'Bukit Tunku'
            ],
            [
                'state_id' => 14,
                'name' => 'Cheras'
            ],
            [
                'state_id' => 14,
                'name' => 'City Centre'
            ],
            [
                'state_id' => 14,
                'name' => 'Country Heights'
            ],
            [
                'state_id' => 14,
                'name' => 'Country Heights Damansara'
            ],
            [
                'state_id' => 14,
                'name' => 'Damansara'
            ],
            [
                'state_id' => 14,
                'name' => 'Damansara Heights'
            ],
            [
                'state_id' => 14,
                'name' => 'Desa Pandan'
            ],
            [
                'state_id' => 14,
                'name' => 'Desa Park City'
            ],
            [
                'state_id' => 14,
                'name' => 'Desa Petaling'
            ],
            [
                'state_id' => 14,
                'name' => 'Jalan Ipoh'
            ],
            [
                'state_id' => 14,
                'name' => 'Jalan Kuching'
            ],
            [
                'state_id' => 14,
                'name' => 'Jalan Sultan Ismail'
            ],
            [
                'state_id' => 14,
                'name' => 'Jinjang'
            ],
            [
                'state_id' => 14,
                'name' => 'Kenny Hills'
            ],
            [
                'state_id' => 14,
                'name' => 'Kepong'
            ],
            [
                'state_id' => 14,
                'name' => 'Keramat'
            ],
            [
                'state_id' => 14,
                'name' => 'KL City'
            ],
            [
                'state_id' => 14,
                'name' => 'KL Sentral'
            ],
            [
                'state_id' => 14,
                'name' => 'KLCC'
            ],
            [
                'state_id' => 14,
                'name' => 'Kuchai Lama'
            ],
            [
                'state_id' => 14,
                'name' => 'Mid Valley City'
            ],
            [
                'state_id' => 14,
                'name' => 'Mont Kiara'
            ],
            [
                'state_id' => 14,
                'name' => 'OUG'
            ],
            [
                'state_id' => 14,
                'name' => 'Pandan Indah'
            ],
            [
                'state_id' => 14,
                'name' => 'Pandan Jaya'
            ],
            [
                'state_id' => 14,
                'name' => 'Pandan Perdana'
            ],
            [
                'state_id' => 14,
                'name' => 'Pantai'
            ],
            [
                'state_id' => 14,
                'name' => 'Pekan Batu'
            ],
            [
                'state_id' => 14,
                'name' => 'Salak Selatan'
            ],
            [
                'state_id' => 14,
                'name' => 'Segambut'
            ],
            [
                'state_id' => 14,
                'name' => 'Sentul'
            ],
            [
                'state_id' => 14,
                'name' => 'Seputeh'
            ],
            [
                'state_id' => 14,
                'name' => 'Setapak'
            ],
            [
                'state_id' => 14,
                'name' => 'Setiawangsa'
            ],
            [
                'state_id' => 14,
                'name' => 'Solaris Dutamas'
            ],
            [
                'state_id' => 14,
                'name' => 'Sri Damansara'
            ],
            [
                'state_id' => 14,
                'name' => 'Sri Hartamas'
            ],
            [
                'state_id' => 14,
                'name' => 'Sri Petaling'
            ],
            [
                'state_id' => 14,
                'name' => 'Sungai Besi'
            ],
            [
                'state_id' => 14,
                'name' => 'Sungai Penchala'
            ],
            [
                'state_id' => 14,
                'name' => 'Taman Desa'
            ],
            [
                'state_id' => 14,
                'name' => 'Taman Duta'
            ],
            [
                'state_id' => 14,
                'name' => 'Taman Melawati'
            ],
            [
                'state_id' => 14,
                'name' => 'Taman Tun Dr Ismail'
            ],
            [
                'state_id' => 14,
                'name' => 'Titiwangsa'
            ],
            [
                'state_id' => 14,
                'name' => 'TPM'
            ],
            [
                'state_id' => 14,
                'name' => 'Wangsa Maju'
            ],
            [
                'state_id' => 14,
                'name' => 'Others'
            ],
            [
                'state_id' => 15,
                'name' => 'Labuan'
            ],
            [
                'state_id' => 16,
                'name' => 'Putrajaya'
            ],
        ]);

        $cities->each(function ($city) {
            Cities::firstOrCreate([
                'state_id' => $city['state_id'],
                'name' => $city['name'],
            ]);
        });
    }
}
