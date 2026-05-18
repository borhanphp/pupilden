<ul class="nav nav-secondary">
    <li class="nav-item">
      <a href="{{route('dashboard.'.auth()->user()->userRole->name)}}">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>
    {{-- <li class="nav-item">
      <a data-bs-toggle="collapse" href="#student">
        <i class="fas fa-pen-square"></i>
        <p>Student</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="student">
        <ul class="nav nav-collapse">
          <li>
            <a href="{{route('students.index')}}">
              <span class="sub-item">Students</span>
            </a>
          </li>
          <li>
            <a href="{{route('course-purchases.index')}}">
              <span class="sub-item">Questions</span>
            </a>
          </li>
        </ul>
      </div>
    </li> --}}
    
    <li class="nav-section">
      <span class="sidebar-mini-icon">
        <i class="fa fa-ellipsis-h"></i>
      </span>
      <h4 class="text-section">Settings</h4>
    </li>
    <li class="nav-item">
      <a data-bs-toggle="collapse" href="#base">
        <i class="fas fa-layer-group"></i>
        <p>Settings</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="base">
        <ul class="nav nav-collapse">
          <li>
            <a href="{{route('organizations.index')}}">
              <span class="sub-item">Organizations</span>
            </a>
          </li>
          <li>
            <a href="{{route('domains.index')}}">
              <span class="sub-item">Domains</span>
            </a>
          </li>
          <li>
            <a href="{{route('coupons.index')}}">
              <span class="sub-item">Coupons</span>
            </a>
          </li>
          <li>
            <a href="{{route('themes.index')}}">
              <span class="sub-item">Themes</span>
            </a>
          </li>
          <li>
            <a href="{{route('pages.index')}}">
              <span class="sub-item">Pages</span>
            </a>
          </li>
          <li>
            <a href="{{route('section-layouts.index')}}">
              <span class="sub-item">Section Layouts</span>
            </a>
          </li>
          <li>
            <a href="{{route('section-contents.index')}}">
              <span class="sub-item">Section Contents</span>
            </a>
          </li>
          <li>
            <a href="{{route('seo-settings.index')}}">
              <span class="sub-item">SEO Settings</span>
            </a>
          </li>
          <li>
            <a href="{{route('media.index')}}">
              <span class="sub-item">Media</span>
            </a>
          </li>
          <li>
            <a href="{{route('sliders.index')}}">
              <span class="sub-item">Sliders</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
   
    <li class="nav-item">
      <a data-bs-toggle="collapse" href="#forms">
        <i class="fas fa-pen-square"></i>
        <p>Course</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="forms">
        <ul class="nav nav-collapse">
          <li>
            <a href="{{route('course-categories.index')}}">
              <span class="sub-item">Categories</span>
            </a>
          </li>
          <li>
            <a href="{{route('course-sub-categories.index')}}">
              <span class="sub-item">Sub Categories</span>
            </a>
          </li>
          <li>
            <a href="{{route('courses.index')}}">
              <span class="sub-item">Courses</span>
            </a>
          </li>
          <li>
            <a href="{{route('course-modules.index')}}">
              <span class="sub-item">Course Modules</span>
            </a>
          </li>
          <li>
            <a href="{{route('course-module-files.index')}}">
              <span class="sub-item">Course Module Files</span>
            </a>
          </li>
          <li>
            <a href="{{route('videos.index')}}">
              <span class="sub-item">Videos</span>
            </a>
          </li>
          <li>
            <a href="{{route('coupons.index')}}">
              <span class="sub-item">Coupons</span>
            </a>
          </li>
          
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a data-bs-toggle="collapse" href="#students">
        <i class="fas fa-user-graduate"></i>
        <p>Student</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="students">
        <ul class="nav nav-collapse">
          <li>
            <a href="{{route('students.index')}}">
              <span class="sub-item">List Students</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a data-bs-toggle="collapse" href="#teachers-menu">
        <i class="fas fa-chalkboard-teacher"></i>
        <p>Teacher</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="teachers-menu">
        <ul class="nav nav-collapse">
          <li>
            <a href="{{route('teachers.index')}}">
              <span class="sub-item">All Teachers</span>
            </a>
          </li>
          <li>
            <a href="{{route('teachers.create')}}">
              <span class="sub-item">Add Teacher</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
    
    
    <li class="nav-item">
      <a data-bs-toggle="collapse" href="#exam">
        <i class="fas fa-pen-square"></i>
        <p>Exam</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="exam">
        <ul class="nav nav-collapse">
          <li>
            <a href="{{route('exams.index')}}">
              <span class="sub-item">Exams</span>
            </a>
          </li>
          <li>
            <a href="{{route('questions.index')}}">
              <span class="sub-item">Questions</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
    
  </ul>