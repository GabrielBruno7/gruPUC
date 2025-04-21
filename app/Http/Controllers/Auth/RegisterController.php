<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use domain\course\Course;
use domain\user\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use infra\CourseDb;
use infra\UserDb;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/groups';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'course_id' => ['required', 'exists:courses,id'],
            'period' => ['required', 'integer', 'min:1', 'max:12'],
        ]);
    }

    protected function create(array $data)
    {
        $course = (new Course(new CourseDb()))
            ->setId($data['course_id'])
            ->loadById()
        ;

        return (new User(new UserDb()))
            ->setCourse($course)
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setPeriod($data['period'])
            ->setPassword($data['password'])
            ->create()
        ;
    }

    public function showRegistrationForm()
    {
        $courses = (new Course(new CourseDb()))->findAllCourses();

        return view('auth.register', compact('courses'));
    }
}
