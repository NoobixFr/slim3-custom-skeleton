<?php
//App/Middleware/CsrfMidlleware.php
namespace App\Middleware;

/**
 * Permet d'implementer facilement les inputs de protection dans un vue
 * Class CsrfViewMiddeware
 * @package App\Middleware
 */
class CsrfViewMiddeware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->container->view->getEnvironment()->addGlobal('csrf', array(
            'field' =>'
                <input type="hidden" name="' . $this->container->csrf->getTokenNameKey() . '" value="' . $this->container->csrf->getTokenName() . '">
                <input type="hidden" name="' . $this->container->csrf->getTokenValueKey() . '" value="' . $this->container->csrf->getTokenValue() . '">
            '
        ));

        $response = $next($request, $response);

        return $response;
    }
}