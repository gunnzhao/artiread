<?php

namespace App\Rss;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use AthosHun\HTMLFilter\Configuration;
use AthosHun\HTMLFilter\HTMLFilter;
use DOMWrap\Document;
use GuzzleHttp\Client;
use Image;
use App\Article;
use App\UserWebsite;
use App\Website as WebsiteModel;

class Item
{
    private $rss;

    private $data;

    public function __construct(Rss $rss, array $data)
    {
        $this->rss = $rss;
        $this->data = $data;
    }

    public function items()
    {
        return $this->data;
    }

    public function list()
    {
        if (!$this->data) {
            return [];
        }

        $config = new Configuration();
        $filter = new HTMLFilter();
        $doc = new Document();

        $list = [];
        foreach ($this->data as $item) {
            $doc->html($item->get_content());

            $list[] = [
                'title' => $item->get_title(),
                'cover_pic' => $doc->find('img')->attr('src'),
                'link' => $item->get_permalink(),
                'description' => Str::limit($filter->filter($config, $item->get_content()), 300),
                'publish_time' => $item->get_date('Y年m月d日 H点i分')
            ];
        }
        return $list;
    }

    public function saveAll()
    {
        if (!$this->rss->getWebsiteId()) {
            return false;
        }

        if (!$this->data) {
            return false;
        }

        $config = new Configuration();
        $filter = new HTMLFilter();
        $doc = new Document();

        $client = new Client([
            'timeout' => 2,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36'
            ],
        ]);

        $updateTime = 0;
        $insertNum = 0;
        
        foreach ($this->data as $item) {
            $data = [];
            
            if (!$item->get_permalink()) {
                continue;
            }

            if (!$item->get_title()) {
                continue;
            }

            if (!$item->get_content()) {
                continue;
            }

            $data['website_id'] = $this->rss->getWebsiteId();
            $data['link'] = $item->get_permalink();
            $data['title'] = $item->get_title();
            $data['cover_pic'] = '';
            $data['description'] = Str::limit($filter->filter($config, $item->get_content()), 300);
            $data['content'] = $item->get_content();
            $data['publish_time'] = $item->get_date('Y-m-d H:i:s');

            if (Article::where('link_md5', md5($item->get_permalink()))->first()) {
                Article::where('link_md5', md5($item->get_permalink()))->update($data);
            } else {
                // 新文章记录，检查是否有文章封面图片进行保存
                $doc->html($item->get_content());
                $src = $doc->find('img')->attr('src');
                if ($src) {
                    try {
                        $response = $client->request('GET', $src);

                        if ($response->getStatusCode() == 200) {
                            list($width, $height, $type) = getimagesizefromstring($response->getBody());
                            // 封面图片必须是jpg或png图片，且宽度不能小于130像素，高度不能小于83像素。
                            if (in_array($type, [2, 3]) and $width >= 130 and $height >= 83) {
                                $img = Image::make($response->getBody());

                                if ($width > 195) {
                                    $img->resize(195, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                    });
                                }

                                $filename = $this->rss->getWebsiteId() . '-' . $item->get_date('YmdHis') . ($type == 2 ? '.jpg' : '.png');
                                $basePath = 'app/public/cover_img/';
                                $savaPath = date('Y-m-d') . '/' . $filename;

                                if (!file_exists(storage_path($basePath))) {
                                    mkdir(storage_path($basePath), 0755);
                                }

                                if (!file_exists(storage_path($basePath . date('Y-m-d')))) {
                                    mkdir(storage_path($basePath . date('Y-m-d')), 0755);
                                }

                                $img->save(storage_path($basePath . $savaPath), 60);
                                $img->destroy();

                                $data['cover_pic'] = $savaPath;
                            }
                        }
                    } catch (\Throwable $e) {}
                }

                $data['link_md5'] = md5($item->get_permalink());
                Article::create($data);
            }
            
            $insertNum++;

            if (strtotime($data['publish_time']) > $updateTime) {
                $updateTime = strtotime($data['publish_time']);
            }
        }

        if (!$insertNum) {
            return false;
        }

        if ($updateTime > 0) {
            if ($lastUpdateTime = $this->rss->getLastUpdateTime()) {
                $carbonTime = new Carbon($updateTime, 'Asia/Shanghai');

                if ($carbonTime->gt($lastUpdateTime)) {
                    // 如果最新的发布日期比数据库中的站点最后更新日期大，则更新数据表对应字段。
                    WebsiteModel::where('id', $this->rss->getWebsiteId())->update(['last_update_time' => $updateTime]);
                    UserWebsite::where('website_id', $this->rss->getWebsiteId())->update(['last_update_time' => $carbonTime]);
                }
            } else {
                WebsiteModel::where('id', $this->rss->getWebsiteId())->update(['last_update_time' => $updateTime]);
            }
        }

        return true;
    }
}