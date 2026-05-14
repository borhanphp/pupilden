<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Remove slider image from R2.
     */
    protected function deleteSliderImageFile(Slider $slider): void
    {
        if (! $slider->image) {
            return;
        }

        $path = str_contains($slider->image, '/')
            ? $slider->image
            : $slider->organization_id.'/sliders/'.$slider->image;

        Storage::disk('r2')->delete($path);
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $folder = auth()->user()->organization_id.'/sliders';
        $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
        $request->file('image')->storeAs($folder, $imageName, 'r2');

        Slider::create([
            'organization_id' => auth()->user()->organization_id,
            'title' => $request->title,
            'description' => $request->description,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => $request->input('sort_order', $slider->sort_order),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            $folder = auth()->user()->organization_id.'/sliders';

            $this->deleteSliderImageFile($slider);

            $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs($folder, $imageName, 'r2');
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
}
