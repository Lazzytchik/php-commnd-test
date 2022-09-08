<?php

namespace Arse\PhpCommandTest;

use Bitrix\Report\VisualConstructor\Entity\DashboardRow;

class TextHandler {

    public static string $separator;

    /**
     * Для каждого пользователя посчитать среднее количество строк в его текстовых файлах и вывести на экран вместе с именем пользователя.
     *
     * @return array
     */
    public static function countAverageLineCount(): array
    {
        //  Достаём массив пользователей из csv файла
        $users = Data::getUsers(self::$separator);

        //  Находим соответствующие этим пользователям текста
        $texts = Data::getTextList($users);

        //  Высчитываем количество строк в найденных файлах
        foreach ($texts as $text){
            $userId = Data::getTextUserId($text);

            if ($file = Data::getText($text)){
                $lines = count(file($file));

                $users[$userId]['lines'] += $lines;
                $users[$userId]['text_count']++;
            }
        }

        //  Считаем среднее для каждого пользователя и выводим результат
        $result = [];
        foreach ($users as $user){
            if ($user['text_count'] > 0){
                $result[$user['name']] = $user['lines'] / $user['text_count'];
            }
            else{
                $result[$user['name']] = 0;
            }
        }

        return $result;
    }

    /**
     * Поместить тексты пользователей в папку ./output_texts, заменив в каждом тексте даты в формате dd/mm/yy на даты в формате mm-dd-yyyy. Вывести на экран количество совершенных для каждого пользователя замен вместе с именем пользователя.
     */
    public static function replaceDates(): array
    {
        //  Возьмём путь до директории выгрузки
        $output = Data::getOutputDirectory();

        //  Возьмём данные о пользователях и текстах
        $users = Data::getUsers(self::$separator);
        $files = Data::getTextList($users);

        //  Подготовим массив с ответом
        $result = array_fill_keys(array_column($users, 'name'), 0);

        if (!file_exists(Data::getOutputDirectory()) && !is_dir(Data::getOutputDirectory()) && !mkdir($concurrentDirectory = Data::getOutputDirectory()) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        foreach ($files as $fileName){
            if ($file = Data::getText($fileName))
            {
                $text = file_get_contents($file);
                $userId = Data::getTextUserId($fileName);

                $count = 0;
                $pattern = "~(\d{2})/(\d{2})/(\d{4})~";
                $text = preg_replace($pattern, "$1-$2-$3", $text, -1, $count);

                $result[$users[$userId]['name']] += $count;

                file_put_contents($output.$fileName, $text);
            }
        }

        return $result;
    }

}
