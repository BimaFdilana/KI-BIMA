<?php

namespace App\Livewire\Admin;

use App\Models\Auth\Notification\NotificationTemplateModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class NotificationTemplateComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $type, $title_template, $message_template, $path_template, $template_id;
    public $is_active = true;
    public $isOpen = false;
    public $isEdit = false;

    protected $rules = [
        'type' => 'required|string|unique:notification_templates,type',
        'title_template' => 'required|string|max:255',
        'message_template' => 'required|string',
        'path_template' => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $templates = NotificationTemplateModel::where('type', 'like', '%' . $this->search . '%')
            ->orWhere('title_template', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.notification-template-component', [
            'templates' => $templates
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
        $this->isEdit = false;
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->type = '';
        $this->title_template = '';
        $this->message_template = '';
        $this->path_template = '';
        $this->is_active = true;
        $this->template_id = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        NotificationTemplateModel::create([
            'id' => (string) Str::uuid(),
            'type' => $this->type,
            'title_template' => $this->title_template,
            'message_template' => $this->message_template,
            'path_template' => $this->path_template,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Template Notifikasi berhasil dibuat.',
            'title' => 'Berhasil!'
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $template = NotificationTemplateModel::findOrFail($id);
        $this->template_id = $id;
        $this->type = $template->type;
        $this->title_template = $template->title_template;
        $this->message_template = $template->message_template;
        $this->path_template = $template->path_template;
        $this->is_active = $template->is_active;

        $this->isEdit = true;
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'type' => 'required|string|unique:notification_templates,type,' . $this->template_id,
            'title_template' => 'required|string|max:255',
            'message_template' => 'required|string',
            'path_template' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $template = NotificationTemplateModel::findOrFail($this->template_id);
        $template->update([
            'type' => $this->type,
            'title_template' => $this->title_template,
            'message_template' => $this->message_template,
            'path_template' => $this->path_template,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Template Notifikasi berhasil diperbarui.',
            'title' => 'Berhasil!'
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        NotificationTemplateModel::find($id)->delete();
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Template Notifikasi berhasil dihapus.',
            'title' => 'Berhasil!'
        ]);
    }

    public function toggleStatus($id)
    {
        $template = NotificationTemplateModel::find($id);
        $template->is_active = !$template->is_active;
        $template->save();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Status berhasil diubah.',
            'title' => 'Berhasil!'
        ]);
    }
}
