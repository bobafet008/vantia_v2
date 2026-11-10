<?php
$extra = '';
if (!empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $params);
    unset($params['pagina']);
    if (!empty($params)) {
        $extra = '&' . http_build_query($params);
    }
}
header('Location: public/index.php?pagina=login' . $extra);
exit;
