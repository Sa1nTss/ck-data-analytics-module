<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;

class QueryHelperService
{
    /**
     * Метод для получения объекта запроса для метода getList.
     */
    public static function setListQuery(QueryBuilder $qb, array $filters, string $prefix, array $order): void
    {
        $page = (empty($filters['page']) || 1 === $filters['page']) ? 0 : $filters['page'] - 1;
        $first_result = (int) $page * (int) ($filters['on_page'] ?? null);

        $limit = (int) ($filters['on_page'] ?? 0);
        $limit = ($limit > 0) ? $limit : null;

        $qb
            ->orderBy($order[0], $order[1])
            ->setFirstResult($first_result)
            ->setMaxResults($limit);
        // ->where("$prefix.delete = false OR $prefix.delete IS NULL");
    }

    /**
     * Метод для получения объекта запроса для метода getListAll.
     */
    public static function setCountQuery(QueryBuilder $qb, string $prefix): void
    {
        $qb
            ->select("COUNT($prefix.id)");
        // ->where("$prefix.delete = false OR $prefix.delete IS NULL");
    }

    /**
     * Format a value that can be used as a parameter for a DQL LIKE search.
     *
     * $qb->where("u.name LIKE (:name) ESCAPE '!'")
     *    ->setParameter('name', $this->makeLikeParam('john'))
     *
     * NOTE: You MUST manually specify the `ESCAPE '!'` in your DQL query, AND the
     * ! character MUST be wrapped in single quotes, else the Doctrine DQL
     * parser will throw an error:
     *
     * [Syntax Error] line 0, col 127: Error: Expected Doctrine\ORM\Query\Lexer::T_STRING, got '"'
     *
     * Using the $pattern argument you can change the LIKE pattern your query
     * matches again. Default is "%search%". Remember that "%%" in a sprintf
     * pattern is an escaped "%".
     *
     * Common usage:
     *
     * ->makeLikeParam('foo')         == "%foo%"
     * ->makeLikeParam('foo', '%s%%') == "foo%"
     * ->makeLikeParam('foo', '%s_')  == "foo_"
     * ->makeLikeParam('foo', '%%%s') == "%foo"
     * ->makeLikeParam('foo', '_%s')  == "_foo"
     *
     * Escapes LIKE wildcards using '!' character:
     *
     * ->makeLikeParam('foo_bar') == "%foo!_bar%"
     *
     * @param string $search  Text to search for LIKE
     * @param string $pattern sprintf-compatible substitution pattern
     */
    public static function makeLikeParam(string $search, string $pattern = '%%%s%%'): string
    {
        /**
         * Function defined in-line, so it doesn't show up for type-hinting on
         * classes that implement this trait.
         *
         * Makes a string safe for use in an SQL LIKE search query by escaping all
         * special characters with special meaning when used in a LIKE query.
         *
         * Uses ! character as default escape character because \ character in
         * Doctrine/DQL had trouble accepting it as a single \ and instead kept
         * trying to escape it as "\\". Resulted in DQL parse errors about "Escape
         * character must be 1 character"
         *
         * % = match 0 or more characters
         * _ = match 1 character
         *
         * Examples:
         *      gloves_pink   becomes  gloves!_pink
         *      gloves%pink   becomes  gloves!%pink
         *      glo_ves%pink  becomes  glo!_ves!%pink
         *
         * @param string $search
         *
         * @return string
         */
        $sanitizeLikeValue = function (string $search) {
            $escapeChar = '!';

            $escape = [
                '\\'.$escapeChar, // Must escape the escape-character for regex
                '\%',
                '\_',
            ];

            $pattern = sprintf('/([%s])/', implode('', $escape));

            return preg_replace($pattern, $escapeChar.'$0', $search);
        };

        return sprintf($pattern, $sanitizeLikeValue($search));
    }

    /**
     * Отдельный метод для стандартной сортировки.
     *
     * @param string|null $sort        параметр сортировки
     * @param string      $prefix      префикс сущности
     * @param string|null $customField параметр обязателен в случае отсутствия поля name, при null значении параметра sort
     */
    public static function getDefaultSort(?string $sort, string $prefix, ?string $customField = null): array
    {
        $sort = (string) $sort;
        $order = '';

        if ($sort) {
            if (str_contains($sort, '__up')) {
                $sort = str_replace('__up', ' ASC', $sort);
            } else {
                $sort .= ' DESC';
            }
            if (!str_contains($sort, '.')) {
                $order = "$prefix.$sort";
            } else {
                $order = $sort;
            }
        } else {
            if (!empty($customField)) {
                $order = "$prefix.$customField ASC";
            } else {
                $order = "$prefix.name ASC";
            }
        }

        return explode(' ', $order);
    }

    public static function validateId($value): bool
    {
        // Проверяем, что строка состоит только из цифр
        if (!ctype_digit($value) or !filter_var($value, FILTER_VALIDATE_INT)) {
            return false;
        }

        // Проверяем длину строки
        $length = strlen($value);
        if ($length < 1 || $length > 9) {
            return false;
        }

        return true;
    }

    public function findMedian(array $numbers): float
    {
        sort($numbers); // Сортируем массив
        $count = count($numbers);
        $middle = floor($count / 2);

        if (0 === $count % 2) {
            // Если количество элементов четное, берем среднее двух центральных значений
            return ($numbers[$middle - 1] + $numbers[$middle]) / 2;
        } else {
            // Если количество элементов нечетное, берем центральное значение
            return $numbers[$middle];
        }
    }
}
