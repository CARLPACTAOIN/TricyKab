@extends('layouts.master')

@section('content')
    <!-- Page Header -->
    <div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
        <div>
            <h4 class="mb-0 text-defaulttextcolor font-medium">Edit Driver</h4>
            <p class="-mt-[0.2rem] mb-0 text-textmuted">Update Driver Information</p>
        </div>
        <div class="main-dashboard-header-right">
            <a href="{{ route('drivers.index') }}" class="ti-btn ti-btn-light !py-1 !px-2 !text-[0.75rem]">
                Back to List
            </a>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Grid -->
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 xl:col-span-8">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Driver Information</h5>
                </div>
                <div class="box-body">
                    <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="ti-form-label">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" class="ti-form-input @error('first_name') !border-red-500 @enderror" id="first_name" name="first_name" placeholder="First Name" value="{{ old('first_name', $driver->first_name) }}">
                                    @error('first_name')
                                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="last_name" class="ti-form-label">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" class="ti-form-input @error('last_name') !border-red-500 @enderror" id="last_name" name="last_name" placeholder="Last Name" value="{{ old('last_name', $driver->last_name) }}">
                                    @error('last_name')
                                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label for="license_number" class="ti-form-label">License Number <span class="text-red-500">*</span></label>
                                <input type="text" class="ti-form-input @error('license_number') !border-red-500 @enderror" id="license_number" name="license_number" placeholder="License Number" value="{{ old('license_number', $driver->license_number) }}">
                                @error('license_number')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="toda_id" class="ti-form-label">TODA Assignment</label>
                                    <select class="ti-form-select @error('toda_id') !border-red-500 @enderror" id="toda_id" name="toda_id">
                                        <option value="">— Select TODA —</option>
                                        @foreach($todas as $toda)
                                            <option value="{{ $toda->id }}" {{ old('toda_id', $driver->toda_id) == $toda->id ? 'selected' : '' }}>{{ $toda->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('toda_id')
                                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="tricycle_id" class="ti-form-label">Assigned Tricycle</label>
                                    <select class="ti-form-select @error('tricycle_id') !border-red-500 @enderror" id="tricycle_id" name="tricycle_id">
                                        <option value="">— Select Tricycle —</option>
                                        @foreach($tricycles as $tricycle)
                                            <option value="{{ $tricycle->id }}" {{ old('tricycle_id', $driver->tricycle_id) == $tricycle->id ? 'selected' : '' }}>{{ $tricycle->body_number }} ({{ $tricycle->plate_number }})</option>
                                        @endforeach
                                    </select>
                                    @error('tricycle_id')
                                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label for="contact_number" class="ti-form-label">Contact Number</label>
                                <input type="text" class="ti-form-input @error('contact_number') !border-red-500 @enderror" id="contact_number" name="contact_number" placeholder="Contact Number" value="{{ old('contact_number', $driver->contact_number) }}">
                                @error('contact_number')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="address" class="ti-form-label">Address</label>
                                <textarea class="ti-form-input @error('address') !border-red-500 @enderror" id="address" name="address" rows="3" placeholder="Address">{{ old('address', $driver->address) }}</textarea>
                                @error('address')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="status" class="ti-form-label">Status</label>
                                <select class="ti-form-select @error('status') !border-red-500 @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $driver->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $driver->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="ti-btn ti-btn-primary-full">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Grid -->
@endsection
