<?php
namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data = UserModel::all();
        $breadcrumb = (object)[
            'title' => 'Manajemen User',
            'list' => ['Dashboard', 'User']
        ];
        
        return view('user.index', compact('data', 'breadcrumb'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'status' => 'required'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        UserModel::create($validated);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = UserModel::findOrFail($id);
        return view('user.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'status' => 'required'
        ]);

        $data = UserModel::findOrFail($id);
        $data->update($validated);

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        UserModel::destroy($id);
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }
}
