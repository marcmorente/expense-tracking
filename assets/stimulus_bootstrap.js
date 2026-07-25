import { startStimulusApp } from "@symfony/stimulus-bundle";

/*
 * Starts Stimulus and registers every controller in assets/controllers/.
 *
 * Register a third-party controller here:
 * app.register("some_controller_name", SomeImportedController);
 */
const app = startStimulusApp();

export { app };
