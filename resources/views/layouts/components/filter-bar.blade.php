<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-sm">
        <div class="flex flex-wrap gap-4 items-center w-full md:w-auto">
            @if(isset($dateFilter))
                <select class="form-select text-sm text-gray-700 bg-gray-50 border-gray-200 rounded-lg focus:ring-[#6258ca] focus:border-[#6258ca] py-2 pl-3 pr-8">
                    <option>Today</option>
                    <option>Last 7 Days</option>
                    <option>April 2026</option>
                </select>
            @endif
            @if(isset($todaFilter))
                <select class="form-select text-sm text-gray-700 bg-gray-50 border-gray-200 rounded-lg focus:ring-[#6258ca] focus:border-[#6258ca] py-2 pl-3 pr-8">
                    <option>All TODAs</option>
                    <option>Poblacion TODA</option>
                    <option>Osias TODA</option>
                    <option>Nongnongan TODA</option>
                </select>
            @endif
            @if(isset($statusFilter))
                <select class="form-select text-sm text-gray-700 bg-gray-50 border-gray-200 rounded-lg focus:ring-[#6258ca] focus:border-[#6258ca] py-2 pl-3 pr-8">
                    <option>All Statuses</option>
                    <option>COMPLETED</option>
                    <option>TRIP_IN_PROGRESS</option>
                    <option>CANCELLED_BY_DRIVER</option>
                </select>
            @endif
            @if(isset($typeFilter))
                <select class="form-select text-sm text-gray-700 bg-gray-50 border-gray-200 rounded-lg focus:ring-[#6258ca] focus:border-[#6258ca] py-2 pl-3 pr-8">
                    <option>All Types</option>
                    <option>SHARED</option>
                    <option>SPECIAL</option>
                </select>
            @endif
            {{ $slot ?? '' }}
        </div>
        <div class="flex space-x-2 w-full md:w-auto">
            <button class="w-full md:w-auto bg-[#f6f6f8] text-gray-700 text-sm font-medium py-2 px-4 rounded-lg border border-gray-200 hover:bg-gray-100 transition whitespace-nowrap">
                Clear
            </button>
            <button class="w-full md:w-auto bg-[#6258ca] hover:bg-indigo-700 text-white text-sm font-medium py-2 px-6 rounded-lg shadow-sm transition whitespace-nowrap">
                Apply Filters
            </button>
        </div>
    </div>
</div>

