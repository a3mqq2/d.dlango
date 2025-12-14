@extends('layouts.auth')

@section('content')
<div class="row">
   <form action="{{ route('login') }}" method="POST">
      @csrf
      @method('POST')
      <div class="col-md-12">
         <div class="text-center">
           <h3 class="text-center mb-3">{{ __('messages.welcome_back') }}</h3>
           <p class="mb-4">{{ __('messages.please_login') }}</p>
         </div>
         <div class="row my-4">

           <div class="col-12">
             <div class="mb-3">
               <label class="form-label">{{ __('messages.email') }}</label>
               <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('messages.enter_email') }}" value="{{ old('email') }}" />
               @error('email')
                   <div class="invalid-feedback">{{ $message }}</div>
               @enderror
             </div>
           </div>

           <div class="col-12">
             <div class="mb-3">
               <label class="form-label">{{ __('messages.password') }}</label>
               <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('messages.enter_password') }}" />
               @error('password')
                   <div class="invalid-feedback">{{ $message }}</div>
               @enderror
             </div>
           </div>

         </div>
         <div class="row g-3">
           <div class="col-sm-12">
             <div class="d-grid">
               <button class="btn" style="background-color: #b65f7a; color: white;" type="submit">{{ __('messages.login') }}</button>
             </div>
           </div>
         </div>
      </div>
   </form>
</div>
@endsection
