<?php

namespace App\Http\Controllers\Integration;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Integration\Integration;
use App\Models\Integration\UserIntegration;
use App\Models\UserOpenai;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    public function index()
    {
        return view('panel.user.integration.index', [
            'items' => Integration::query()->with('extension')->get(),
        ]);
    }

    public function edit(Integration $integration)
    {
        $class = $integration->getFormClassName();

        if (! class_exists($class)) {
            abort(404);
        }

        $userItem = UserIntegration::query()
            ->firstOrCreate([
                'user_id' => Auth::id(),
                'integration_id' => $integration->getAttribute('id'),
            ], [
                'credentials' => $class::form(),
            ]);

        return view('panel.user.integration.edit', [
            'item' => $integration,
            'userItem' => $userItem,
            'credentials' => $userItem->credentials ?: $class::form(),
        ]);
    }

    public function update(Request $request, Integration $integration)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $class = $integration->getFormClassName();

        if (! class_exists($class)) {
            abort(404);
        }

        $userIntegration = UserIntegration::query()
            ->where('user_id', Auth::id())
            ->where('integration_id', $integration->getAttribute('id'))
            ->first();

        $userIntegration->update([
            'credentials' => $class::form($request->all()),
        ]);

        return redirect()->route('dashboard.user.integration.index')->with('success', 'Integration updated successfully');
    }

    public function workbook(UserIntegration $userIntegration, UserOpenai $userOpenai)
    {
        $openai = $userOpenai->generator;

        $integration = $userIntegration->integration;

        $class = $integration->getFormClassName();

        if (! class_exists($class)) {
            abort(404);
        }

        $service = new $class($userIntegration);

        if ($service->login() === false) {
            return back()->with([
                'type' => 'error',
                'message' => trans('Invalid credentials. Please check your credentials and try again.'),
            ]);
        }

        $categories = $service->category();
        $tags = $service->tags();
        $images = $service->images();

        return view('panel.user.integration.documents_workbook', [
            'workbook' => $userOpenai,
            'openai' => $openai,
            'userIntegration' => $userIntegration,
            'title' => trans('Share to ').$integration->getAttribute('app'),
            'categories' => $categories,
            'tags' => $tags,
            'images' => $images,
        ]);
    }

    public function storeWorkbook(Request $request, UserIntegration $userIntegration, UserOpenai $userOpenai)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type' => 'info',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request->validate([
            'title' => 'required|string',
            'workbook_text' => 'required|string',
        ]);

        $class = $userIntegration->integration->getFormClassName();

        if (! class_exists($class)) {
            abort(404);
        }

        $service = new $class($userIntegration);

        if ($service->login() === false) {
            return back()->with([
                'type' => 'error',
                'message' => trans('Invalid credentials. Please check your credentials and try again.'),
            ]);
        }

        if ($request->get('date_gmt')) {
            $date = Carbon::createFromFormat('Y-m-d\TH:i', $request->get('date_gmt'), config('app.timezone'));

            $date->setTimezone('UTC');

            $gmtDateTime = $date->toIso8601String();
        }

        $service->create([
            'title' => $request->get('title'),
            'content' => $request->get('workbook_text'),
            'status' => $request->get('status'),
            'comment_status' => $request->get('comment_status'),
            'categories' => $request->get('categories'),
            'tags' => $request->get('tags'),
            'featured_media' => $request->get('featured_media'),
            'date_gmt' => $gmtDateTime ?? null,
        ]);

        return redirect()->back()->with('success', trans('Document created successfully'));
    }

    public function storeImage(Request $request, UserIntegration $userIntegration, UserOpenai $userOpenai)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type' => 'info',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $class = $userIntegration->integration->getFormClassName();

        if (! class_exists($class)) {
            abort(404);
        }

        $service = new $class($userIntegration);

        if ($service->login() === false) {
            return back()->with([
                'type' => 'error',
                'message' => trans('Invalid credentials. Please check your credentials and try again.'),
            ]);
        }

        $imagePath = $request->get('image');

        try {
            if (str_starts_with($imagePath, '/uploads')) {
                $tempFilePath = realpath(public_path($imagePath));
            } else {
                $client = new Client();
                $response = $client->get($imagePath);
                $tempFilePath = tempnam(sys_get_temp_dir(), 'image');

                file_put_contents($tempFilePath, $response->getBody()->getContents());
                $tempFilePath = realpath($tempFilePath);
            }

            $response = $service->addImage([
                'file' => fopen($tempFilePath, 'r'),
                'title' => basename($imagePath),
            ]);

            if (isset($response)) {
                return redirect()->back()->with('success', trans('Document created successfully'));
            } else {
                throw new Exception('Error while creating post: '.json_encode($response));
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return back()->with([
                'type' => 'error',
                'message' => 'Guzzle error: '.$e->getMessage(),
            ]);
        } catch (Exception $e) {
            return back()->with([
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
