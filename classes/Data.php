<?php

namespace Arse\PhpCommandTest;

use JetBrains\PhpStorm\Pure;

class Data
{
    private const DIRECTORY = 'data/';
    private const TEXTS_DIRECTORY = 'texts/';
    private const OUTPUT_DIRECTORY = 'output_texts/';

    /**
     * Возвращает массив пользователей из файла.
     *
     * @param string $separator
     * @param string $userFile
     * @return array
     */
    public static function getUsers(string $separator, string $userFile = "people.csv"): array
    {
        $users = [];
        if (($handle = fopen(self::DIRECTORY.$userFile, 'rb')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                $users[$data[0]] = ['name' => $data[1], 'text_count' => 0, 'lines' => 0];
            }
            fclose($handle);
        }

        return $users;
    }

    /**
     * Достаёт массив названий файлов текстов.
     *
     * @param array $users
     *
     * @return bool|array
     */
    public static function getTextList(array $users): bool|array
    {
        return preg_grep("~^".implode('|', array_keys($users))."-.*\.txt~", scandir(self::getTextsDirectory()));
    }


    public static function getDirectory(): string
    {
        return self::DIRECTORY;
    }

    /**
     * Возвращает директорию хранения текстов.
     *
     * @return string
     */
    public static function getTextsDirectory(): string
    {
        return self::DIRECTORY.self::TEXTS_DIRECTORY;
    }

    /**
     * Возвращает директорию выдачи текстов.
     *
     * @return string
     */
    public static function getOutputDirectory(): string
    {
        return self::DIRECTORY.self::OUTPUT_DIRECTORY;
    }

    /**
     * Проверяет, существует ли текст в директории.
     *
     * @param string $file
     *
     * @return string|bool
     */
    #[Pure] public static function getText(string $file): string|bool
    {
        $path = self::getTextsDirectory().$file;
        if (file_exists($path)){
            return $path;
        }
        return false;
    }

    /**
     * Возвращает id пользователя-автора текста.
     *
     * @param $text
     *
     * @return string
     */
    public static function getTextUserId($text): string
    {
        return explode('-', $text)[0];
    }



}