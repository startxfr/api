<?php

/**
 * This class deliver method for handling training families informations
 * availables url are
 * - http://api.startx.fr/v1/api/training/family        -> GET all families informations
 * - http://api.startx.fr/v1/api/training/family        -> POST create a new family
 * - http://api.startx.fr/v1/api/training/family/xxxx   -> GET detail information for a particular id
 * - http://api.startx.fr/v1/api/training/family/xxxx   -> PUT update the family
 * - http://api.startx.fr/v1/api/training/family/xxxx   -> DELETE delete a family
 */
class training_FamilyRessource extends default_DefaultRessource {

    private $right = array(
        'read'=> 5,
        'create'=> 100,
        'update'=> 100,
        'delete'=> 100
    );

    public function read($request, $renderer) {
        $id = (int) $request->getElement(2);
        if ($request->getSession()->isAutorized($this->right['read'])) {
            try {
                $model = new RhformfamilyModel();
                if ($id != '')
                    $renderer->renderOk("training webservice returning informations about training family " . $id, $model->readOne($id));
                else
                    $renderer->renderOk("training webservice returning informations about training families", $model->read());
            } catch (Exception $e) {
                $renderer->renderError(900, "can't read training family " . $e->getMessage(), $e);
            }
        }
        else
            $renderer->renderError(900,"you are not autorized to perform read action for training family webservice", $request->getSession());
    }

    public function create($request, $renderer) {
        if ($request->getSession()->isAutorized($this->right['create'])) {
        try {
            $model = new RhformfamilyModel();
            $return = $model->create($request->getParams());
            if ($return !== false)
                $renderer->renderOk("training webservice added new training family " . $return, $model->readOne($return));
            else
                $renderer->renderError(900, "db error when create training family ", $return);
        } catch (Exception $e) {
            $renderer->renderError(900, "can't create training family " . $e->getMessage(), $e);
        }
        }
        else
            $renderer->renderError(900,"you are not autorized to perform create action for training family webservice", false);
    }

    public function update($request, $renderer) {
        $id = (int) $request->getElement(2);
        if ($request->getSession()->isAutorized($this->right['update'])) {
        try {
            $model = new RhformfamilyModel();
            $return = $model->update($id, $request->getParams());
            if ($return !== false)
                $renderer->renderOk("training webservice updated training family " . $id, $model->readOne($id));
            else
                $renderer->renderError(900, "db error when updating training family " . $id, $return);
        } catch (Exception $e) {
            $renderer->renderError(900, "can't update training family $id : " . $e->getMessage(), $e);
        }
        }
        else
            $renderer->renderError(900,"you are not autorized to perform update action for training family webservice", false);
    }

    public function delete($request, $renderer) {
        $id = (int) $request->getElement(2);
        if ($request->getSession()->isAutorized($this->right['delete'])) {
        try {
            $model = new RhformfamilyModel();
            $return = $model->delete($id);
            if ($return !== false)
                $renderer->renderOk("training webservice delete training family " . $id, $return);
            else
                $renderer->renderError(900, "db error when deleting training family " . $id, $return);
        } catch (Exception $e) {
            $renderer->renderError(900, "can't delete training family $id : " . $e->getMessage(), $e);
        }
        }
        else
            $renderer->renderError(900,"you are not autorized to perform delete action for training family webservice", false);
    }

}

?>
