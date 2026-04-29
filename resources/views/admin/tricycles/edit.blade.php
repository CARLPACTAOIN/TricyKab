@extends('layouts.master')

@section('content')
    <!-- Page Header -->
    <div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
        <div>
            <h4 class="mb-0 text-defaulttextcolor font-medium">Edit Tricycle</h4>
            <p class="-mt-[0.2rem] mb-0 text-textmuted">Update Tricycle Information</p>
        </div>
        <div class="main-dashboard-header-right">
            <a href="{{ route('tricycles.index') }}" class="ti-btn ti-btn-light !py-1 !px-2 !text-[0.75rem]">
                Back to List
            </a>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Grid -->
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 xl:col-span-6">
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">Tricycle Information</h5>
                </div>
                <div class="box-body">
                    <form action="{{ route('tricycles.update', $tricycle->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="body_number" class="ti-form-label">Body Number</label>
                                <input type="text" class="ti-form-input @error('body_number') !border-red-500 @enderror" id="body_number" name="body_number" placeholder="Enter Body Number" value="{{ old('body_number', $tricycle->body_number) }}">
                                @error('body_number')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="plate_number" class="ti-form-label">Plate Number</label>
                                <input type="text" class="ti-form-input @error('plate_number') !border-red-500 @enderror" id="plate_number" name="plate_number" placeholder="Enter Plate Number" value="{{ old('plate_number', $tricycle->plate_number) }}">
                                @error('plate_number')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="toda_id" class="ti-form-label">TODA</label>
                                <select class="ti-form-select @error('toda_id') !border-red-500 @enderror" id="toda_id" name="toda_id">
                                    <option value="">Select TODA</option>
                                    @foreach($todas as $toda)
                                        <option value="{{ $toda->id }}" {{ old('toda_id', $tricycle->toda_id) == $toda->id ? 'selected' : '' }}>{{ $toda->name }}</option>
                                    @endforeach
                                </select>
                                @error('toda_id')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="make_model" class="ti-form-label">Make & Model</label>
                                <input type="text" class="ti-form-input @error('make_model') !border-red-500 @enderror" id="make_model" name="make_model" placeholder="e.g. Honda TMX 155" value="{{ old('make_model', $tricycle->make_model) }}">
                                @error('make_model')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="status" class="ti-form-label">Status</label>
                                <select class="ti-form-select @error('status') !border-red-500 @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $tricycle->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $tricycle->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status', $tricycle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="registration_status" class="ti-form-label">LTO Registration Status</label>
                                <select class="ti-form-select @error('registration_status') !border-red-500 @enderror" id="registration_status" name="registration_status">
                                    @foreach(['ACTIVE', 'EXPIRED', 'PENDING', 'SUSPENDED'] as $ltoStatus)
                                        <option value="{{ $ltoStatus }}" {{ old('registration_status', $tricycle->registration_status ?? 'ACTIVE') === $ltoStatus ? 'selected' : '' }}>{{ $ltoStatus }}</option>
                                    @endforeach
                                </select>
                                @error('registration_status')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="capacity" class="ti-form-label">Capacity</label>
                                <input type="number" class="ti-form-input @error('capacity') !border-red-500 @enderror" id="capacity" name="capacity" min="1" max="8" value="{{ old('capacity', $tricycle->capacity ?? 4) }}">
                                @error('capacity')
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
