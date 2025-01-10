<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

trait LinkService
{
    private function createLinkString(array $queryData): string
    {
        $out = '';

        foreach ($queryData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $keyItem => $valueItem) {
                    if (is_array($valueItem)) {
                        foreach ($valueItem as $kkk => $vvv) {
                            $out .= $key.'['.$keyItem.']['.$kkk.']='.$vvv.'&';
                        }
                    } else {
                        $out .= $key.'['.$keyItem.']='.$valueItem.'&';
                    }
                }
            } else {
                $out .= $key.'='.$value.'&';
            }
        }

        return strlen($out) > 0 ? '?'.$out : '?';
    }

    public function getParinatorLink(): string
    {
        $queryData = $this->getLinkParam();

        if (!empty($queryData['page'])) {
            unset($queryData['page']);
        }
        if (!empty($queryData['ajax'])) {
            unset($queryData['ajax']);
        }

        return $this->createLinkString($queryData).'page=';
    }

    public function getSortLink(): string
    {
        $queryData = $this->getLinkParam();
        if (!empty($queryData['sort'])) {
            unset($queryData['sort']);
        }
        if (!empty($queryData['page'])) {
            unset($queryData['page']);
        }

        return $this->createLinkString($queryData).'sort=';
    }

    public function getCSVLink(): string
    {
        $queryData = $this->getLinkParam();

        if (!empty($queryData['page'])) {
            unset($queryData['page']);
        }

        return $this->createLinkString($queryData);
    }

    public function getSearchLink(): string
    {
        $queryData = $this->getLinkParam();
        if (!empty($queryData['sort'])) {
            unset($queryData['sort']);
        }
        if (!empty($queryData['page'])) {
            unset($queryData['page']);
        }
        if (isset($queryData['search'])) {
            unset($queryData['search']);
        }
        if (isset($queryData['extended_search'])) {
            unset($queryData['extended_search']);
        }

        return $this->createLinkString($queryData);
    }

    private function getLinkParam(): array
    {
        $request = new Request($_GET);

        return $request->query->all();
    }

}
