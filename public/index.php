<?php

require dirname(__DIR__).'/vendor/autoload.php';

$url = 'https://www.helloasso.com/associations/aspas-association-pour-la-protection-des-animaux-sauvages/collectes/vercors-vie-sauvage';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url
]);

$resp = curl_exec($curl);

curl_close($curl);

$c = new \Symfony\Component\DomCrawler\Crawler($resp);

$amount = (int) preg_replace('~\D~', '', $c->filter('[data-action="auto-grow"]')->text());
$goal = (int) preg_replace('~\D~', '', $c->filter('p.goal strong')->text());

$req = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$percent = number_format(100 * $amount / $goal, 0, '', ' ');
$amount = number_format($amount, 0, '', ' ');
$goal = number_format($goal, 0, '', ' ');

$txt = sprintf('%s € récoltés sur %s €, soit %s %%', $amount, $goal, $percent);

if ($req->isXmlHttpRequest()) {
    echo $txt;
    exit;
}

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vercors vie sauvage</title>
    <style type="text/css">
        * { margin: 0; padding: 0; }
    </style>
    <script type="text/javascript">
        var request = new XMLHttpRequest();
        request.open('GET', window.location.href, true);
        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                document.body.innerHTML = this.response;
            }
        };
        request.send();
    </script>
</head>
<body>
    <?= $txt ?>
</body>
</html>
