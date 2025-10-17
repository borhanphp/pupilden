<?php

namespace App\Http\Controllers;

use App\Models\CourseModuleFile;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseModuleFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseModuleFile::with(['courseModule']);

        // Filter by course module if course_module_id is provided
        if ($request->has('course_module_id') && $request->course_module_id) {
            $query->where('course_module_id', $request->course_module_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('file_type', 'like', '%' . $request->search . '%')
                  ->orWhere('file_extension', 'like', '%' . $request->search . '%');
            });
        }

        $courseModuleFiles = $query->orderBy('created_at', 'desc')->paginate(15);
        $courseModules = CourseModule::with('course')->get();

        return view('course-module-files.index', compact('courseModuleFiles', 'courseModules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $courseModules = CourseModule::with('course')->get();
        $selectedCourseModuleId = $request->get('course_module_id');
        
        return view('course-module-files.create', compact('courseModules', 'selectedCourseModuleId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_module_id' => 'required|exists:course_modules,id',
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $folder = auth()->user()->organization_id . '/course_module_files';
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }
            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . $originalName . '.' . $extension;
            
            // Store file
            $filePath = $file->storeAs($folder, $fileName, 'public');
            
            // Get file info
            $data['file_path'] = $filePath;
            $data['file_type'] = $this->getFileType($extension);
            $data['file_size'] = $file->getSize();
            $data['file_url'] = asset('uploads/' . auth()->user()->organization_id . '/course_module_files/' . $fileName);
            $data['file_extension'] = $extension;
            $data['file_mime_type'] = $file->getMimeType();
        }

        CourseModuleFile::create($data);

        return redirect()->route('course-module-files.index')
            ->with('success', 'Course module file uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseModuleFile $courseModuleFile)
    {
        $courseModuleFile->load(['courseModule.course']);
        
        return view('course-module-files.show', compact('courseModuleFile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseModuleFile $courseModuleFile)
    {
        $courseModules = CourseModule::with('course')->get();
        
        return view('course-module-files.edit', compact('courseModuleFile', 'courseModules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseModuleFile $courseModuleFile)
    {
        $request->validate([
            'course_module_id' => 'required|exists:course_modules,id',
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        // Handle file upload if new file is provided
        if ($request->hasFile('file')) {
            $folder = auth()->user()->organization_id . '/course_module_files';
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }
            // Delete old file if exists
            if ($courseModuleFile->file_path && Storage::disk('public')->exists($folder . '/' . $courseModuleFile->file_path)) {
                Storage::disk('public')->delete($folder . '/' . $courseModuleFile->file_path);
            }

            $file = $request->file('file');
            
            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . $originalName . '.' . $extension;
            
            // Store file
            $filePath = $file->storeAs($folder, $fileName, 'public');
            
            // Get file info
            $data['file_path'] = $filePath;
            $data['file_type'] = $this->getFileType($extension);
            $data['file_size'] = $file->getSize();
            $data['file_url'] = asset('uploads/' . auth()->user()->organization_id . '/course_module_files/' . $fileName);
            $data['file_extension'] = $extension;
            $data['file_mime_type'] = $file->getMimeType();
        }

        $courseModuleFile->update($data);

        return redirect()->route('course-module-files.index')
            ->with('success', 'Course module file updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseModuleFile $courseModuleFile)
    {
        // Delete associated file if exists
        if ($courseModuleFile->file_path && Storage::disk('public')->exists(auth()->user()->organization_id . '/course_module_files/' . $courseModuleFile->file_path)) {
            Storage::disk('public')->delete(auth()->user()->organization_id . '/course_module_files/' . $courseModuleFile->file_path);
        }

        $courseModuleFile->delete();

        return redirect()->route('course-module-files.index')
            ->with('success', 'Course module file deleted successfully.');
    }

    /**
     * Download the file
     */
    public function download(CourseModuleFile $courseModuleFile)
    {
        if (!$courseModuleFile->file_path || !Storage::disk('public')->exists(auth()->user()->organization_id . '/course_module_files/' . $courseModuleFile->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download(storage_path('app/public/' . $courseModuleFile->file_path), $courseModuleFile->name . '.' . $courseModuleFile->file_extension);
    }

    /**
     * Get file type based on extension
     */
    private function getFileType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'];
        $presentationExtensions = ['ppt', 'pptx', 'odp'];
        $spreadsheetExtensions = ['xls', 'xlsx', 'ods', 'csv'];

        $extension = strtolower($extension);

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        } elseif (in_array($extension, $presentationExtensions)) {
            return 'presentation';
        } elseif (in_array($extension, $spreadsheetExtensions)) {
            return 'spreadsheet';
        } else {
            return 'other';
        }
    }
}
