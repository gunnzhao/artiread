<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Website;
use App\Rss\Rss;

class UpdateArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:update {websiteId=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新站点新发布的文章';

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
        $startTime = microtime(true);

        Log::channel('article_update')->info('-----Article update start-----');
        Log::channel('article_update')->info('定时更新任务开始！');
        $websiteId = $this->argument('websiteId');

        if ($websiteId == 0) {
            $websites = Website::where('status', 0)->get();
            if ($websites->count() == 0) {
                Log::channel('article_update')->info('没有站点记录');
                return;
            }

            $retris = [];
            foreach ($websites as $website) {
                Log::channel('article_update')->info('[' . $website->id . ']' . '开始更新站点：' . $website->name . ' ' . $website->url);

                $feed = new Rss($website->url);
                if ($feed->hasError()) {
                    Log::channel('article_update')->error('[' . $website->id . ']' . $website->name . ' 访问失败');
                    Log::channel('article_update')->error('[' . $website->id . '] FAILED ' . $feed->hasError());
                    $retris[] = $website;
                    continue;
                }

                $feed->setWebsiteModel($website);
                $feed->item->saveAll();
                Log::channel('article_update')->info('[' . $website->id . ']' . $website->name . ' 更新完成');
            }

            if (!empty($retris)) {
                Log::channel('article_update')->info('-----重试访问失败的站点-----');

                foreach ($retris as $website) {
                    Log::channel('article_update')->info('[' . $website->id . ']' . '开始重试更新站点：' . $website->name . ' ' . $website->url);

                    $feed = new Rss($website->url);
                    if ($feed->hasError()) {
                        Log::channel('article_update')->error('[' . $website->id . ']' . $website->name . ' 访问失败');
                        Log::channel('article_update')->error('[' . $website->id . '] FAILED ' . $feed->hasError());
                        continue;
                    }

                    $feed->setWebsiteModel($website);
                    $feed->item->saveAll();
                    Log::channel('article_update')->info('[' . $website->id . ']' . $website->name . ' 重试更新完成');
                }
            }
        } else {
            $website = Website::where([
                ['id', $websiteId], ['status', 0]
            ])->first();
            if (!$website) {
                Log::channel('article_update')->error('不存在该站点');
                return;
            }

            Log::channel('article_update')->info('[' . $website->id . ']' . '开始更新站点：' . $website->name . ' ' . $website->url);

            $feed = new Rss($website->url);
            if ($feed->hasError()) {
                Log::channel('article_update')->error('[' . $website->id . ']' . $website->name . ' 访问失败');
                Log::channel('article_update')->error('[' . $website->id . '] FAILED ' . $feed->hasError());
                $retris[] = $website;
                return;
            }

            $feed->setWebsiteModel($website);
            $feed->item->saveAll();
            Log::channel('article_update')->info('[' . $website->id . ']' . $website->name . ' 更新完成');
        }

        $endTime = microtime(true);
        if ($websiteId == 0) {
            Log::channel('article_update')->info('执行了' . $websites->count() . '个站点，' . '总用时：' . ($endTime - $startTime));
        } else {
            Log::channel('article_update')->info('执行了1个站点，总用时：' . ($endTime - $startTime) . '秒');
        }
        Log::channel('article_update')->info('-----Article update end-----');
    }
}
