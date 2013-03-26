<?php

/**
 * This class deliver method for handling training dates informations
 * availables url are
 * - http://api.startx.fr/v1/api/training/dates        -> GET all dates informations
 * - http://api.startx.fr/v1/api/training/dates        -> POST create a new date
 * - http://api.startx.fr/v1/api/training/dates/$id    -> GET detail information for a particular id
 * - http://api.startx.fr/v1/api/training/dates/$id    -> PUT update the training date
 * - http://api.startx.fr/v1/api/training/dates/$id    -> DELETE delete a training date
 */
class training_DatesRessource extends default_DefaultRessource {

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
                $model = new RhformdatesModel();
                if ($id != '')
                    $renderer->renderOk("training webservice returning informations about training date " . $id, $model->readOne($id));
                else
                    $renderer->renderOk("training webservice returning informations about training dates", $model->read());
            } catch (Exception $e) {
                $renderer->renderError(900, "can't read training date " . $e->getMessage(), $e);
            }
        }
        else
            $renderer->renderError(900, "you are not autorized to perform read action for training webservice", false);
    }

    public function create($request, $renderer) {
        if ($request->getSession()->isAutorized($this->right['create'])) {
            try {
                $model = new RhformdatesModel();
                $return = $model->create($request->getParams());
                if ($return !== false)
                    $renderer->renderOk("training webservice added new training date " . $return, $model->readOne($return));
                else
                    $renderer->renderError(900, "db error when create training date ", $return);
            } catch (Exception $e) {
                $renderer->renderError(900, "can't create training date " . $e->getMessage(), $e);
            }
        }
        else
            $renderer->renderError(900, "you are not autorized to perform create action for training dates webservice", false);
    }

    public function update($request, $renderer) {
        $id = (int) $request->getElement(2);
        if ($request->getSession()->isAutorized($this->right['update'])) {
            try {
                $model = new RhformdatesModel();
                $return = $model->update($id, $request->getParams());
                if ($return !== false)
                    $renderer->renderOk("training webservice updated training date " . $id, $model->readOne($id));
                else
                    $renderer->renderError(900, "db error when updating training date " . $id, $return);
            } catch (Exception $e) {
                $renderer->renderError(900, "can't update training date $id : " . $e->getMessage(), $e);
            }
        }
        else
            $renderer->renderError(900, "you are not autorized to perform update action for training dates webservice", false);
    }

    public function delete($request, $renderer) {
        $id = (int) $request->getElement(2);
        if ($request->getSession()->isAutorized($this->right['delete'])) {
            try {
                $model = new RhformdatesModel();
                $return = $model->delete($id);
                if ($return !== false)
                    $renderer->renderOk("training webservice delete training date " . $id, $return);
                else
                    $renderer->renderError(900, "db error when deleting training date " . $id, $return);
            } catch (Exception $e) {
                $renderer->renderError(900, "can't delete training date $id : " . $e->getMessage(), $e);
            }
        }
        else
            $renderer->renderError(900, "you are not autorized to perform delete action for training dates webservice", false);
    }

}

?>
