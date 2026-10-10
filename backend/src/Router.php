<?php
namespace Jenniferd\NutritionApi;

class Router{
    private array $routes = [];

    public function get(string $path, callable $handler): void{
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void{
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch($method, $path){
        if (!isset($this->routes[$method][$path])){
            echo 'Route not found';
            return;
        }

        $handler = $this->routes[$method][$path];

        $handler();
    }
}
?>