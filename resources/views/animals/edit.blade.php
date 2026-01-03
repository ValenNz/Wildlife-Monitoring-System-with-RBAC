<!-- resources/views/animals/modals/edit.blade.php -->
<div id="edit-animal-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Animal</h3>
            <button onclick="closeModal('edit-animal-modal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <form id="edit-animal-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $animal->id }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ $animal->name }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <div class="text-red-500 text-xs mt-1 error-message" data-field="name"></div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Species</label>
                <input type="text" name="species" value="{{ $animal->species }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <div class="text-red-500 text-xs mt-1 error-message" data-field="species"></div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tag ID</label>
                <input type="text" name="tag_id" value="{{ $animal->tag_id }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <div class="text-red-500 text-xs mt-1 error-message" data-field="tag_id"></div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Device ID (Optional)</label>
                <input type="text" name="device_id" value="{{ $animal->device_id ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <div class="text-red-500 text-xs mt-1 error-message" data-field="device_id"></div>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('edit-animal-modal')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('edit-animal-form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear errors
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        const formData = new FormData(form);
        const url = "{{ route('animals.update.ajax', ':id') }}".replace(':id', "{{ $animal->id }}");

        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            closeModal('edit-animal-modal');
            location.reload();
        } else {
            Object.keys(result.errors).forEach(field => {
                const el = document.querySelector(`[data-field="${field}"]`);
                if (el) el.textContent = result.errors[field][0];
            });
        }
    });
});
</script>
