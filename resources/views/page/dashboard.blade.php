@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.home') @endsection

@section('content')
<?php 
    use Jenssegers\Agent\Agent;
    $agent = new Agent();
?>

<style>
.dashboardImgMobile {
  width: 100%;
  content: "";
  background: url('/assets/images/taskBgImage.png') no-repeat center center fixed;
  background-position: center 120px;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: 100% auto;
  background-color: #F4F6F8;
}
.dashboardImgDesktop {
  width: 100%;
  content: "";
  background: url('/assets/images/task-management-tool.webp') no-repeat center center fixed;
  background-position: center 180px;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: 40% auto;
  background-color: white;
}
</style>

<div class="{{ $agent->isMobile() ? 'dashboardImgMobile' : 'dashboardImgDesktop' }}" style="background-color: white !important; min-height:100vh;">
</div>
@endsection

@section('script')
@endsection

