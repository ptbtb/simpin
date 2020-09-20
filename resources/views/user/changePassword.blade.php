@extends('adminlte::page')
@section('title')
	{{ $title }}
@endsection

@section('content_header')
<h4>{{ $title }}</h4>
@endsection

@section('css')
    <style>
        .custom-label{
            margin-top: 0.5rem
        }

        .form-control{
            height: calc(2rem + 2px);
        }

        .error-message{
            font-size: 12px;
            color: red;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <form action="{{ route('user-change-password') }}" method="post">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-md-4 text-right">
                                <label class="custom-label">Old Password</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="old_password" class="form-control" id="oldPassword" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4 text-right">
                                <label class="custom-label">New Password</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="new_password" class="form-control" id="newPassword" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4 text-right">
                                <label class="custom-label">Confirm New Password</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="confirm_new_password" class="form-control" id="confirmNewPassword" required>
                                <label class="error-message" id="confNewPassErrorMessage" style="display: none"></label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-sm btn-success">Change Password</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script>
        $('#newPassword').keyup(function () {
            var newPass = $(this).val();
            var confPass = $('#confirmNewPassword').val();
            if ( confPass !== '' && confPass !== newPass)
            {
                var message = "passwords do not match"
                $('#confNewPassErrorMessage').html(message);
                $('#confNewPassErrorMessage').show();
            }
            else
            {
                $('#confNewPassErrorMessage').html('');
                $('#confNewPassErrorMessage').hide();
            }
        });
        $('#confirmNewPassword').keyup(function () {
            var confPass = $(this).val();
            var newPass = $('#newPassword').val();
            if (confPass !== newPass)
            {
                var message = "passwords do not match"
                $('#confNewPassErrorMessage').html(message);
                $('#confNewPassErrorMessage').show();
            }
            else
            {
                $('#confNewPassErrorMessage').html('');
                $('#confNewPassErrorMessage').hide();
            }
        });
    </script>
@endsection