<?php
namespace App\Livewire\Presenter;

use App\Models\Presenter;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class PresenterList extends Component
{
    use WithFileUploads;

    public $presenters;
    public $showModal = false;
    public $name;
    public $work;
    public $description;
    public $image;
    public $socialPlatforms = [];
    public $editingPresenter = null;
    public $sortField = 'name';
    public $sortDirection = 'asc';


    public function mount()
    {
        $this->loadPresenters();
    }
    public function loadPresenters()
    {
        $this->presenters = Presenter::orderBy($this->sortField, $this->sortDirection)->get();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->loadPresenters();
    }


    public function openModal($presenterId = null)
    {
        if ($presenterId) {
            $this->editingPresenter = Presenter::find($presenterId);
            $this->name = $this->editingPresenter->name;
            $this->work = $this->editingPresenter->work;
            $this->description = $this->editingPresenter->description;
            $this->socialPlatforms = json_decode($this->editingPresenter->social_platforms, true) ?? [];
        } else {
            $this->resetForm(); // Clear form for adding new presenter
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function addSocialPlatform()
    {
        $this->socialPlatforms[] = ['name' => '', 'link' => '', 'svg' => ''];
    }

    public function removeSocialPlatform($index)
    {
        unset($this->socialPlatforms[$index]);
        $this->socialPlatforms = array_values($this->socialPlatforms);
    }

    public function deletePresenter($id)
    {
        $presenter = Presenter::find($id);
        if ($presenter) {
            // Delete image if it exists
            if ($presenter->image) {
                Storage::disk('public')->delete($presenter->image);
            }

            // Delete the presenter from the database
            $presenter->delete();

            // Refresh the list of presenters
            $this->presenters = Presenter::all();
        }
    }

    public function savePresenter()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'work' => 'required|string|max:255',
            'description' => 'required|string',
            'socialPlatforms.*.name' => 'required|string|max:255',
            'socialPlatforms.*.link' => 'required|url',
            'socialPlatforms.*.svg' => 'nullable|regex:/^<svg[\s\S]*<\/svg>$/',
        ];

        // Make image optional if editing an existing presenter
        if ($this->editingPresenter) {
            $rules['image'] = 'nullable|image|max:1024'; // Image is optional
        } else {
            $rules['image'] = 'required|image|max:1024'; // Image is required for new presenter
        }

        $validatedData = $this->validate($rules, [
            'socialPlatforms.*.name.required' => 'اسم المنصة مطلوب.',
            'socialPlatforms.*.link.required' => 'الرابط مطلوب.',
            'socialPlatforms.*.link.url' => 'الرابط يجب أن يكون URL صالح.',
            'socialPlatforms.*.svg.regex' => 'صيغة الحقل غير صحيحة. يجب أن يكون SVG',
        ]);

        $imagePath = $this->image
            ? $this->image->store('presenters', 'public')
            : ($this->editingPresenter ? $this->editingPresenter->image : null);

        if ($this->editingPresenter) {
            // Update existing presenter
            $this->editingPresenter->update([
                'name' => $this->name,
                'work' => $this->work,
                'description' => $this->description,
                'image' => $imagePath,
                'social_platforms' => json_encode($this->socialPlatforms),
            ]);
            $this->dispatch('presenter-updated');
        } else {
            // Create new presenter
            Presenter::create([
                'name' => $this->name,
                'work' => $this->work,
                'description' => $this->description,
                'image' => $imagePath,
                'social_platforms' => json_encode($this->socialPlatforms),
            ]);
            $this->dispatch('presenter-created');

        }

        $this->presenters = Presenter::all();
        $this->closeModal();
        $this->dispatch('presenter-saved');
    }


    private function resetForm()
    {
        $this->name = null;
        $this->work = null;
        $this->description = null;
        $this->image = null;
        $this->socialPlatforms = [];
        $this->editingPresenter = null; // Reset the editing presenter
    }

    public function render()
    {
        return view('livewire.presenter.presenter-list');
    }
}
