<?php

$segment1 = service('uri')->getSegment(1);
?>
<nav>
    <div class="nav-wrapper teal">
        <a href="<?= site_url('/') ?>" class="brand-logo" style="margin-left:12px;">UMG</a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li class="<?= $segment1 === 'alumnos' ? 'active' : '' ?>">
                <a href="<?= site_url('alumnos') ?>">Alumnos</a>
            </li>
            <li class="<?= $segment1 === 'cursos' ? 'active' : '' ?>">
                <a href="<?= site_url('cursos') ?>">Cursos</a>
            </li>
        </ul>
    </div>
</nav>