<?php

/*
 * This file is part of FeatherPanel.
 *
 * Copyright (C) 2025 MythicalSystems Studios
 * Copyright (C) 2025 FeatherPanel Contributors
 * Copyright (C) 2025 Cassian Gherman (aka NaysKutzu)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See the LICENSE file or <https://www.gnu.org/licenses/>.
 */

namespace App\Plugins\Mixins\Reflection;

use App\App;
use ReflectionClass;

/**
 * Provides runtime class patching and modification capabilities.
 *
 * This class allows for dynamic modification of classes, methods, and properties
 * at runtime without modifying the original source code.
 */
class ClassPatcher
{
    /** // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar array Cached reflection classes */
    private static array $reflectionCache = [];

    /** // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar array Method patches by class and method name */
    private static array $methodPatches = [];

    /** // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar array Class proxies by class name */
    private static array $classProxies = [];

    /** // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar array Property overrides by class and property name */
    private static array $propertyOverrides = [];

    /**
     * Get a ReflectionClass instance for a class.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string|object $class Class name or object instance
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn \ReflectionClass The reflection class
     */
    public static function getReflectionClass($class): \ReflectionClass
    {
        $className = is_object($class) ? get_class($class) : $class;

        if (!isset(self::$reflectionCache[$className])) {
            self::$reflectionCache[$className] = new \ReflectionClass($className);
        }

        return self::$reflectionCache[$className];
    }

    /**
     * Apply a patch to a method.
     *
     * This method allows you to override, extend, or modify the behavior of a method
     * at runtime without changing the original class definition.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $methodName The method name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam \Closure $patchCallback The patch callback
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $patchType The patch type: 'before', 'after', 'around', or 'replace'
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if successfully patched
     */
    public static function patchMethod(string $className, string $methodName, \Closure $patchCallback, string $patchType = 'around'): bool
    {
        $logger = App::getInstance(true)->getLogger();

        try {
            // Validate the patch type
            if (!in_array($patchType, ['before', 'after', 'around', 'replace'])) {
                $logger->warning("Invalid patch type: {$patchType}");

                return false;
            }

            // Check if the method exists
            $reflection = self::getReflectionClass($className);
            if (!$reflection->hasMethod($methodName)) {
                $logger->warning("Method {$methodName} does not exist in class {$className}");

                return false;
            }

            $method = $reflection->getMethod($methodName);

            // Store the patch
            $key = "{$className}::{$methodName}";
            if (!isset(self::$methodPatches[$key])) {
                self::$methodPatches[$key] = [];
            }

            self::$methodPatches[$key][] = [
                'callback' => $patchCallback,
                'type' => $patchType,
                'method' => $method,
            ];

            $logger->debug("Patched method {$className}::{$methodName} with {$patchType} patch");

            return true;
        } catch (\Throwable $e) {
            $logger->error("Failed to patch method {$className}::{$methodName}: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Apply a patch before a method is executed.
     *
     * The callback receives the same arguments as the original method.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $methodName The method name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam \Closure $callback The callback to execute before the method
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if successfully patched
     */
    public static function beforeMethod(string $className, string $methodName, \Closure $callback): bool
    {
        return self::patchMethod($className, $methodName, $callback, 'before');
    }

    /**
     * Apply a patch after a method is executed.
     *
     * The callback receives the original method's return value as the first argument,
     * followed by the original method arguments.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $methodName The method name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam \Closure $callback The callback to execute after the method
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if successfully patched
     */
    public static function afterMethod(string $className, string $methodName, \Closure $callback): bool
    {
        return self::patchMethod($className, $methodName, $callback, 'after');
    }

    /**
     * Replace a method entirely.
     *
     * The callback will be used instead of the original method.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $methodName The method name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam \Closure $callback The replacement method
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if successfully patched
     */
    public static function replaceMethod(string $className, string $methodName, \Closure $callback): bool
    {
        return self::patchMethod($className, $methodName, $callback, 'replace');
    }

    /**
     * Apply a patch around a method.
     *
     * The callback receives a callable for the original method as the first argument,
     * followed by the original method arguments. The callback is responsible for
     * calling the original method if needed.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $methodName The method name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam \Closure $callback The callback to execute around the method
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if successfully patched
     */
    public static function aroundMethod(string $className, string $methodName, \Closure $callback): bool
    {
        return self::patchMethod($className, $methodName, $callback, 'around');
    }

    /**
     * Execute method with patches applied.
     *
     * This is used internally to apply the registered patches when a method is called.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam object $instance The object instance
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $methodName The method name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $args The method arguments
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn mixed The method return value
     */
    public static function executeMethod(object $instance, string $methodName, array $args)
    {
        $className = get_class($instance);
        $key = "{$className}::{$methodName}";

        // If no patches exist, just call the original method
        if (!isset(self::$methodPatches[$key]) || empty(self::$methodPatches[$key])) {
            return $instance->$methodName(...$args);
        }

        // Apply patches
        $result = null;
        $patches = self::$methodPatches[$key];

        // Execute 'before' patches
        foreach ($patches as $patch) {
            if ($patch['type'] === 'before') {
                $patch['callback']->call($instance, ...$args);
            }
        }

        // Execute 'around' or 'replace' patches
        $aroundExecuted = false;
        foreach ($patches as $patch) {
            if ($patch['type'] === 'around') {
                $aroundExecuted = true;

                // Create a callable for the original method
                $originalMethod = function (...$methodArgs) use ($instance, $methodName) {
                    // Skip all patches when calling the original from an around patch
                    return $instance->$methodName(...$methodArgs);
                };

                // Call the around patch with the original method and arguments
                $result = $patch['callback']->call($instance, $originalMethod, ...$args);
                break;
            } elseif ($patch['type'] === 'replace') {
                $aroundExecuted = true;
                $result = $patch['callback']->call($instance, ...$args);
                break;
            }
        }

        // If no around/replace patches were executed, call the original method
        if (!$aroundExecuted) {
            $result = $instance->$methodName(...$args);
        }

        // Execute 'after' patches
        foreach ($patches as $patch) {
            if ($patch['type'] === 'after') {
                $newResult = $patch['callback']->call($instance, $result, ...$args);
                // Allow after patches to modify the return value
                if ($newResult !== null) {
                    $result = $newResult;
                }
            }
        }

        return $result;
    }

    /**
     * Override a property value.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $propertyName The property name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam mixed $value The new value
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if successfully overridden
     */
    public static function overrideProperty(string $className, string $propertyName, $value): bool
    {
        $logger = App::getInstance(true)->getLogger();

        try {
            // Check if the property exists
            $reflection = self::getReflectionClass($className);
            if (!$reflection->hasProperty($propertyName)) {
                $logger->warning("Property {$propertyName} does not exist in class {$className}");

                return false;
            }

            $property = $reflection->getProperty($propertyName);

            // Store the override
            $key = "{$className}::{$propertyName}";
            self::$propertyOverrides[$key] = [
                'value' => $value,
                'property' => $property,
            ];

            $logger->debug("Overrode property {$className}::{$propertyName}");

            return true;
        } catch (\Throwable $e) {
            $logger->error("Failed to override property {$className}::{$propertyName}: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Get a property value with overrides applied.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam object $instance The object instance
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $propertyName The property name
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn mixed The property value
     */
    public static function getPropertyValue(object $instance, string $propertyName)
    {
        $className = get_class($instance);
        $key = "{$className}::{$propertyName}";

        // If an override exists, use it
        if (isset(self::$propertyOverrides[$key])) {
            return self::$propertyOverrides[$key]['value'];
        }

        // Otherwise, get the actual property value
        $reflection = self::getReflectionClass($instance);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }

    /**
     * Create a proxy for a class.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $methodOverrides Method overrides
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $propertyOverrides Property overrides
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn string The proxy class name
     */
    public static function createClassProxy(string $className, array $methodOverrides = [], array $propertyOverrides = []): string
    {
        $logger = App::getInstance(true)->getLogger();

        try {
            $reflection = self::getReflectionClass($className);
            $proxyClassName = $className . 'Proxy' . uniqid();

            // Generate the proxy class
            $code = self::generateProxyClass($reflection, $proxyClassName, $methodOverrides, $propertyOverrides);

            // Evaluate the proxy class code
            eval($code);

            // Store the proxy class
            self::$classProxies[$className] = $proxyClassName;

            $logger->debug("Created proxy class {$proxyClassName} for {$className}");

            return $proxyClassName;
        } catch (\Throwable $e) {
            $logger->error("Failed to create proxy class for {$className}: " . $e->getMessage());

            return $className;
        }
    }

    /**
     * Get a proxy instance for a class.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $className The class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $constructorArgs Constructor arguments
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn object The proxy instance
     */
    public static function getProxyInstance(string $className, array $constructorArgs = []): object
    {
        $logger = App::getInstance(true)->getLogger();

        try {
            // If a proxy exists, use it
            if (isset(self::$classProxies[$className])) {
                $proxyClass = self::$classProxies[$className];

                return new $proxyClass(...$constructorArgs);
            }

            // Otherwise, create a new instance of the original class
            return new $className(...$constructorArgs);
        } catch (\Throwable $e) {
            $logger->error("Failed to get proxy instance for {$className}: " . $e->getMessage());

            return new $className(...$constructorArgs);
        }
    }

    /**
     * Generate a proxy class.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam \ReflectionClass $reflection The reflection class
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $proxyClassName The proxy class name
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $methodOverrides Method overrides
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $propertyOverrides Property overrides
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn string The proxy class code
     */
    private static function generateProxyClass(\ReflectionClass $reflection, string $proxyClassName, array $methodOverrides, array $propertyOverrides): string
    {
        $namespace = $reflection->getNamespaceName();
        $shortName = $reflection->getShortName();

        $code = "namespace {$namespace};\n\n";
        $code .= "class {$shortName}Proxy" . substr($proxyClassName, strrpos($proxyClassName, 'Proxy') + 5) . " extends {$shortName} {\n";

        // Add property overrides
        foreach ($propertyOverrides as $propertyName => $value) {
            $code .= "    private \${$propertyName}Override = " . var_export($value, true) . ";\n";

            // Add getter method
            $code .= '    public function get' . ucfirst($propertyName) . "() {\n";
            $code .= "        return \$this->{$propertyName}Override;\n";
            $code .= "    }\n";

            // Add setter method
            $code .= '    public function set' . ucfirst($propertyName) . "(\$value) {\n";
            $code .= "        \$this->{$propertyName}Override = \$value;\n";
            $code .= "    }\n";
        }

        // Add method overrides
        foreach ($methodOverrides as $methodName => $override) {
            $method = $reflection->getMethod($methodName);
            $parameters = [];

            foreach ($method->getParameters() as $param) {
                $paramStr = '';

                if ($param->hasType()) {
                    $type = $param->getType();
                    if ($type instanceof \ReflectionNamedType) {
                        $typeName = $type->getName();
                        $paramStr .= $typeName . ' ';
                    }
                }

                $paramStr .= '$' . $param->getName();

                if ($param->isDefaultValueAvailable()) {
                    $defaultValue = $param->getDefaultValue();
                    $paramStr .= ' = ' . var_export($defaultValue, true);
                }

                $parameters[] = $paramStr;
            }

            $paramList = implode(', ', $parameters);
            $returnType = '';
            if ($method->hasReturnType()) {
                $type = $method->getReturnType();
                if ($type instanceof \ReflectionNamedType) {
                    $returnType = ': ' . $type->getName();
                }
            }

            $code .= "    public function {$methodName}({$paramList}){$returnType} {\n";
            $code .= "        // Method override for {$methodName}\n";
            $code .= "        {$override}\n";
            $code .= "    }\n";
        }

        $code .= "}\n";

        return $code;
    }
}
