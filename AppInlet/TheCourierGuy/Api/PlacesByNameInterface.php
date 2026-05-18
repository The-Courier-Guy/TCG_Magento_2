<?php

namespace AppInlet\TheCourierGuy\Api;

interface PlacesByNameInterface
{
    /**
     * GET for Post api
     *
     * @return array|bool|string
     */
    public function getPlacesByName(): array|bool|string;
}
