<?php
// Komenda do uruchomienia: php processingWord.php

/**
 * Losowo miesza litery wewnątrz słowa, pozostawiając pierwszą i ostatnią literę bez zmian.
 *
 * @param string $word Pojedyncze słowo do przetasowania.
 * @return string Przetasowane słowo.
 */
function scrambleWord(string $word): string
{
    if (mb_strlen($word) <= 3) return $word;

    $chars = preg_split('//u', mb_substr($word, 1, -1), -1, PREG_SPLIT_NO_EMPTY);
    shuffle($chars);

    return mb_substr($word, 0, 1) . implode('', $chars) . mb_substr($word, -1);
}

/**
 * Przetwarza pojedynczą linię tekstu, mieszając litery w wyrazach.
 * Interpunkcja i odstępy pozostają bez zmian.
 *
 * @param string $line Linia tekstu do przetworzenia.
 * @return string Przetworzona linia tekstu.
 */
function scrambleLine(string $line): string
{
    return implode('', array_map(
        fn($token) => preg_match('/^\pL+$/u', $token) ? scrambleWord($token) : $token,
        preg_split('/(\PL+)/u', $line, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)
    ));
}

/**
 * Wczytuje plik wejściowy linia po linii, przetwarza każdą linię
 * i zapisuje wynik do nowego pliku tekstowego.
 *
 * @param string $input  Ścieżka do pliku wejściowego.
 * @param string $output Ścieżka do pliku wynikowego.
 * @return void
 */
function processFile(string $input, string $output): void
{
    $in = fopen($input, 'r');
    $out = fopen($output, 'w');

    if (!$in || !$out) exit("Błąd otwierania pliku.\n");

    while (($line = fgets($in)) !== false) {
        fwrite($out, scrambleLine($line));
    }

    fclose($in);
    fclose($out);

    echo "Plik zapisany do: $output\n";
}

// Nazwy plików do przetworzenia
processFile('input.txt', 'output.txt');
