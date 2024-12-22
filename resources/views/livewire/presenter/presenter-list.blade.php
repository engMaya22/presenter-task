<div class="container mx-auto" dir="rtl">
    <div class="w-full p-4 mb-2 text-lg font-bold bg-gray-200 ">المتقدمين</div>

    <div class="flex items-center mb-4">
        <!-- Search Input -->

        <div class="relative flex-1">
            <input
                id="search"
                type="text"
                placeholder="اكتب اسم المتقدم..."
                class="inline-block p-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <button
            wire:click="openModal"
            class="px-4 py-2 ml-2 mr-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
            اضافة متقدم
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-lg ">
        <table class="w-full text-right border-collapse table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th
                        class="px-4 py-2 border border-gray-300 cursor-pointer"
                        wire:click="sortBy('name')">
                        الاسم
                        @if ($sortField === 'name')
                            @if ($sortDirection === 'asc')
                                <i class="fas fa-sort-up"></i>
                            @else
                                <i class="fas fa-sort-down"></i>
                            @endif
                        @endif
                    </th>
                    <th class="px-4 py-2 border border-gray-300">العمل</th>
                    <th class="px-4 py-2 border border-gray-300">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($presenters as $presenter)
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 border border-gray-300">{{ $presenter->name }}</td>
                        <td class="px-4 py-2 border border-gray-300">{{ $presenter->work }}</td>
                        <td class="px-4 py-2 border border-gray-300">
                            <!-- Dropdown button -->
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" class="px-3 py-1 text-white bg-gray-300 rounded hover">
                                    خيارات
                                    <i class="ml-2 text-gray-600 fas fa-chevron-down"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 w-32 mt-1 bg-white rounded-lg shadow-lg">
                                    <div class="">
                                        <button wire:click="openModal({{ $presenter->id }})" class="flex items-center px-2 py-1 text-sm text-gray-700 px- hover:bg-gray-100">
                                            <span>تعديل</span>

                                            <i class="mr-2 text-blue-500 fas fa-edit"></i>
                                        </button>
                                        <button wire:click="deletePresenter({{ $presenter->id }})" class="flex items-center px-2 py-1 text-sm text-red-500 hover:bg-gray-100">
                                            <span>حذف</span>
                                            <i class="mr-2 text-red-500 fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center border border-gray-300">لا توجد بيانات.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
       <!-- Modal -->
       @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75">
            <div class="w-1/3 max-h-screen p-6 overflow-y-auto bg-white rounded-lg shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-center">{{ $editingPresenter ? 'تعديل مقدم' : 'إضافة مقدم' }}</h3>

                <form wire:submit.prevent="savePresenter">
                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">الصورة</label>
                        <input type="file" wire:model="image" class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('image') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                        @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="mt-4 rounded-md" style="max-width: 100%; max-height: 200px;">
                        @elseif($editingPresenter && $editingPresenter->image)
                        <img src="{{ asset('storage/' . $editingPresenter->image) }}" alt="Preview" class="mt-4 rounded-md" style="max-width: 100%; max-height: 200px;">
                        @endif
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">الاسم</label>
                        <input type="text" wire:model="name" class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Work -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">العمل</label>
                        <input type="text" wire:model="work" class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('work') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">الوصف</label>
                        <textarea wire:model="description" rows="3" class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('description') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Social Platforms -->
                    @foreach ($socialPlatforms as $index => $socialPlatform)
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <!-- Name Field -->
                        <div>
                            <label for="name-{{ $index }}" class="block mb-1 text-sm font-medium text-gray-700">اسم المنصة</label>
                            <input
                                type="text"
                                id="name-{{ $index }}"
                                wire:model="socialPlatforms.{{ $index }}.name"
                                placeholder="اسم المنصة"
                                class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <!-- Link Field -->
                        <div>
                            <label for="link-{{ $index }}" class="block mb-1 text-sm font-medium text-gray-700">رابط المنصة</label>
                            <input
                                type="url"
                                id="link-{{ $index }}"
                                wire:model="socialPlatforms.{{ $index }}.link"
                                placeholder="رابط المنصة"
                                class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <!-- SVG Field -->
                        <div>
                            <label for="svg-{{ $index }}" class="block mb-1 text-sm font-medium text-gray-700">SVG كود</label>
                            <input
                                type="text"
                                id="svg-{{ $index }}"
                                wire:model="socialPlatforms.{{ $index }}.svg"
                                placeholder="أدخل كود SVG"
                                class="block w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <!-- Remove Button -->
                        <div class="flex items-center">
                            <button
                                type="button"
                                wire:click="removeSocialPlatform({{ $index }})"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600"
                            >
                                حذف
                            </button>
                        </div>
                    </div>
                   @endforeach


                    <button type="button" wire:click="addSocialPlatform" class="px-4 py-2 mb-4 text-white bg-green-500 rounded-lg hover:bg-green-600">
                        إضافة منصة اجتماعية
                    </button>

                    <div class="text-center">
                        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                            {{ $editingPresenter ? ' حغظ' : 'إضافة ' }}
                        </button>

                    </div>
                </form>
            </div>
        </div>
    @endif


</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('presenter-created',()=>{
        Swal.fire({
            title: "تم اضافة المقدم بنجاح",
            icon: "success"
        });
    })
    window.addEventListener('presenter-updated',()=>{
        Swal.fire({
            title: "تم تعديل المقدم بنجاح",
            icon: "success"
        });
    })
</script>

