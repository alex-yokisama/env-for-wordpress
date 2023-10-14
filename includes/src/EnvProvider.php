<?php 

namespace EnvForWordpress;

interface EnvProvider {
    public function init(): void;
    public function get(string $key);
}
