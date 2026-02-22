@extends('layouts.master')

@section('content')
    <!-- Page Header -->
    <div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
        <div>
            <h4 class="mb-0 text-defaulttextcolor font-medium">Create TODA</h4>
            <p class="-mt-[0.2rem] mb-0 text-textmuted">Add a new Tricycle Operators and Drivers Association</p>
        </div>
        <div class="main-dashboard-header-right">
            <a href="{{ route('todas.index') }}" class="ti-btn ti-btn-light !py-1 !px-2 !text-[0.75rem]">
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
                    <h5 class="box-title">TODA Information</h5>
                </div>
                <div class="box-body">
                    <form action="{{ route('todas.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="ti-form-label">TODA Name <span class="text-red-500">*</span></label>
                                <input type="text" class="ti-form-input @error('name') !border-red-500 @enderror" id="name" name="name" placeholder="e.g. Poblacion TODA" value="{{ old('name') }}">
                                @error('name')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="area_coverage" class="ti-form-label">Area Coverage (Barangay)</label>
                                <input type="text" class="ti-form-input @error('area_coverage') !border-red-500 @enderror" id="area_coverage" name="area_coverage" placeholder="e.g. Brgy. Poblacion, Brgy. Osias" value="{{ old('area_coverage') }}">
                                @error('area_coverage')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="operating_hours" class="ti-form-label">Operating Hours</label>
                                <input type="text" class="ti-form-input @error('operating_hours') !border-red-500 @enderror" id="operating_hours" name="operating_hours" placeholder="e.g. 5:00 AM - 10:00 PM" value="{{ old('operating_hours') }}">
                                @error('operating_hours')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="status" class="ti-form-label">Status</label>
                                <select class="ti-form-select @error('status') !border-red-500 @enderror" id="status" name="status">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="ti-btn ti-btn-primary-full">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Grid -->
@endsection
