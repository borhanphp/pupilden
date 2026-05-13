<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Remove slider image from disk (supports filename-only like Course, or legacy full storage path).
     */
    protected function deleteSliderImageFile(Slider $slider): void
    {
        if (! $slider->image) {
            return;
        }

        if (str_contains($slider->image, '/')) {
            if (Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            return;
        }

        $folder = $slider->organization_id.'/sliders';
        $path = $folder.'/'.$slider->image;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function ensureOrganization(Slider $slider): void
    {
        if ($slider->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
    }

    public function index()
    {
        $sliders = Slider::where('organization_id', auth()->user()->organization_id)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('sliders.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|string|max:600',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $folder = auth()->user()->organization_id.'/sliders';
        if (! Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
        $request->file('image')->storeAs($folder, $imageName, 'public');

        Slider::create([
            'organization_id' => auth()->user()->organization_id,
            'title' => $request->title,
            'description' => $request->description,
            'link' => ($link = trim((string) $request->input('link', ''))) !== '' ? $link : null,
            'image' => $imageName,
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sliders.index')
            ->with('success', 'Slider created successfully.');
    }

    public function edit(Slider $slider)
    {
        $this->ensureOrganization($slider);

        return view('sliders.form', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $this->ensureOrganization($slider);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|string|max:600',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'link' => ($link = trim((string) $request->input('link', ''))) !== '' ? $link : null,
            'sort_order' => $request->input('sort_order', $slider->sort_order),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            $folder = auth()->user()->organization_id.'/sliders';
            if (! Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            $this->deleteSliderImageFile($slider);

            $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs($folder, $imageName, 'public');
            $data['image'] = $imageName;
        }

        $slider->update($data);

        return redirect()->route('sliders.index')
            ->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        $this->ensureOrganization($slider);

        $this->deleteSliderImageFile($slider);

        $slider->delete();

        return redirect()->route('sliders.index')
            ->with('success', 'Slider deleted successfully.');
    }

    /**
     * AJAX upload for embedded images in the slider description (Summernote).
     */
    public function uploadDescriptionImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $orgId = (string) auth()->user()->organization_id;
        $folder = $orgId.'/sliders/description';
        if (! Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $extension = $request->file('file')->getClientOriginalExtension() ?: 'jpg';
        $filename = uniqid('desc_', true).'.'.$extension;
        $request->file('file')->storeAs($folder, $filename, 'public');

        $url = asset('uploads/'.$folder.'/'.$filename);

        return response()->json(['url' => $url]);
    }
}
