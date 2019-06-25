<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Image;
use App\Article;

class DownloadCover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:cover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文章封面图片下载(临时升级使用)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $records = Article::where('cover_pic', '!=', '')->get();
        foreach ($records as $record) {
            if (strpos($record->cover_pic, '?')) {
                $srcArr = explode('?', $record->cover_pic);
                $record->cover_pic = $srcArr[0];
            }

            $client = new Client([
                'timeout' => 1,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36'
                ],
            ]);

            try {
                $response = $client->request('GET', $record->cover_pic);

                if ($response->getStatusCode() == 200) {
                    list($width, $height, $type) = getimagesizefromstring($response->getBody());
                    
                    $img = Image::make($response->getBody());
                    if ($width > 195) {
                        $img->resize(195, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }

                    $filename = $record->website_id . '-' . $record->publish_time->format('YmdHis') . ($type == 2 ? '.jpg' : '.png');
                    $basePath = 'app/public/cover_img/';
                    $savePath = $record->created_at->format('Y-m-d') . '/' . $filename;

                    if (!file_exists(storage_path($basePath))) {
                        mkdir(storage_path($basePath), 0755);
                    }

                    if (!file_exists(storage_path($basePath . $record->created_at->format('Y-m-d')))) {
                        mkdir(storage_path($basePath . $record->created_at->format('Y-m-d')), 0755);
                    }

                    $img->save(storage_path($basePath . $savePath), 60);

                    $record->cover_pic = $savePath;
                    $record->save();
                } else {
                    $record->cover_pic = '';
                    $record->save();
                }
            } catch (\Throwable $e) {
                $record->cover_pic = '';
                $record->save();
            }
        }
    }
}
