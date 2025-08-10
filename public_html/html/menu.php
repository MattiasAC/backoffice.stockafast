<?php
$menus = array();
$menus["tenants"] = ["Hyreslista", "/tenants/"];
$menus["lokaler"] = ["Lokaler", "/lokaler/"];
$menus["meters"] = ["Elmätare", "/meters/"];
$menus["elfaktura"] = ["Elfakturor", "/el/"];
$menus["spot"] = ["Spotpriser", "/spot/"];
$menus["kontrakt"] = ["Kontrakt", "/kontrakt/"];
$menus["kontrakat"] = ["Kontrakt D", "/kontrakt/defaults/"];


function buildMenu($items, $parentId = null) {
    $currentLink = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $menuHtml = '<ul class="nav flex-column text-white p-3">';
    foreach ($items as $key => $item) {
        $hasChildren = empty($item[1]);
        $submenuId = $parentId ? $parentId . '_' . $key : $key;
        $isActive = !$hasChildren && $item[1] === $currentLink;
        $expandedClass = ($hasChildren && hasActiveChild($item["sub"], $currentLink)) ? ' show' : '';

        if ($hasChildren) {
            $menuHtml .= '
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-bs-toggle="collapse" data-bs-target="#' . $submenuId . '" role="button" aria-expanded="false" aria-controls="' . $submenuId . '">' . $item[0] . '</a>
                <div class="collapse' . $expandedClass . '" id="' . $submenuId . '">
                    ' . buildMenu($item["sub"], $submenuId) . '
                </div>
            </li>';
        } else {
            $activeClass = $isActive ? ' active' : '';
            $menuHtml .= '
            <li class="nav-item">
                <a class="nav-link text-white '.$activeClass.'" href="' . $item[1] . '">' . $item[0] . '</a>
            </li>';
        }
    }
    $menuHtml .= '</ul>';
    return $menuHtml;
}
function hasActiveChild($items, $currentUrl) {
        foreach ($items as $child) {
            if(isset($child[1])){
                if ($child[1] === $currentUrl) {
                    return true;
                }
            }
            else{
                if (hasActiveChild($child["sub"],$currentUrl)) {
                    return true;
                }
            }
        }
    return false;
}
$menuItems = buildMenu($menus);
?>

<nav id="mainMenu" class="d-none d-lg-flex text-bg-menu vh-100 position-fixed" style="width: 200px;margin: 0px;padding: 0px;"">
    <?= $menuItems; ?>
</nav>

<nav class="navbar navbar-dark bg-dark d-lg-none">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?= $menuItems; ?>
        </div>
    </div>
</nav>



