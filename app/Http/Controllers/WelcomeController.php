<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WelcomeController extends Controller
{

	public function index()
	{

		$this->delete('1573032292test.jpg');

		$url = 'https://example.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
		$images = [];
		$files = Storage::disk('s3')->files('images');

		foreach ($files as $file) {
			$images[] = [
				'name' => str_replace('images/', '', $file),
				'src' => $url . $file
			];
		}

		//dd(json_encode($images));
		//echo json_encode($images);
		//exit;
		return view('welcome', compact('images'));
	}

	// Save image
	public function store(Request $request)
	{
		$this->validate($request, [
			'image' => 'required|image|max:10240'
		]);
		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$name = time() . $file->getClientOriginalName();
			$filePath = 'images/' . $name;
			Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');

			//$imageName = Storage::disk('s3')->url($filePath);
			//dd($imageName);

		}
		return back()->withSuccess('Image uploaded successfully');
	}

	// Delete image
	public function delete($image)
	{
		$exists = Storage::disk('s3')->exists('images/' . $image);
		if ($exists) {
			Storage::disk('s3')->delete('images/' . $image);
			return back()->withSuccess('Image was deleted successfully');
		}
	}
}
