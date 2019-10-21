Web app with implemented A/B testing suit. Uses Symfony event listeners and cookies.
Requires a Docker and Docker-Compose on host machine to run.


To run, enter in app directory:
````
make build && make run
````

By default app will start on `http://localhost:8080`

To set up A/B cases see: `src/EventListener/StickySessionControllerArgumentsListener.php:15`
. Here you can set hypotheses names and their probability densities.
Go to `http://localhost:8080/` to test. You will see the result as you were switched to one of the cases.

To use in your code, just:  
1) Implement `getAbTestableActions` in desired controller. This method must return array of actions you want to test.

2) Add one unused argument to action you want to test. Hypothesis name will be automatically injected in this argument.
