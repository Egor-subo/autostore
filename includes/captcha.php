<?php

function refresh_captcha(): void
{
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_answer'] = $a + $b;
    $_SESSION['captcha_question'] = "$a + $b = ?";
}

function captcha_question(): string
{
    if (empty($_SESSION['captcha_question'])) {
        refresh_captcha();
    }

    return $_SESSION['captcha_question'];
}

function check_captcha(string $answer): bool
{
    return isset($_SESSION['captcha_answer']) && ((int)$answer === (int)$_SESSION['captcha_answer']);
}
