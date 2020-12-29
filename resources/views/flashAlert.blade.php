{{-- flash alert --}}
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
  <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
  <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
  <strong>{{ $message }}</strong>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
  <button type="button" class="close" data-dismiss="alert">×</button> 
  {{-- Please check the form below for errors --}}
  <ul style="list-style-type: circle; margin: 0; padding-left: 1rem;">
    {!! implode('', $errors->all('<li>:message</li>')) !!}
  </ul>
</div>
@endif