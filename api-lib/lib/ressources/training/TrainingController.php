<?php

class training_TrainingRessource extends default_DefaultRessource {

    private $right = array(
        'read'=> 5
    );

    public function read($request, $renderer) {
        if ($request->getSession()->isAutorized($this->right['read'])) {
            $renderer->renderOk("This ressource implement courses,cursus,dates action", "This ressource implement courses,cursus,dates action");
        }
        else
            $renderer->renderError(900, "you are not autorized to perform read action for training webservice", false);
        return true;
    }

}

?>
