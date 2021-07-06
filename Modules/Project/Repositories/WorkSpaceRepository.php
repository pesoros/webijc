<?php

namespace Modules\Project\Repositories;

use Illuminate\Validation\ValidationException;
use Modules\Project\Entities\Workspace;
use Auth;

class WorkSpaceRepository
{
    private $model;

    public function __construct(Workspace $model)
    {
        $this->model = $model;
    }

    public function create($data){
        return $this->model->forceCreate($data);
    }

    public function findOrFail($id, $field = 'message')
    {
        $model = $this->model->find($id);
        if (! $model) {
            throw ValidationException::withMessages([$field => __('project::workspace.not_found')]);
        }
        return $model;
    }

    function currentWorkSpaceAllMembers($id)
    {
        $model = $this->model->find($id);

        return $model->allUsers();
    }

    public function allWorkspace()
    {
        $user = Auth::user();
        $Workspaces = [];
        $arr = [];
        foreach($user->teams as $key => $team){
            if(!in_array($team->workspace_id, $arr)){
                array_push($Workspaces, $team->Workspace);
                array_push($arr, $team->workspace_id);
            }else{
                continue;
            }
            
        }

        $ownWorkspaces = $this->model->where('user_id', Auth::id())->get();
        foreach ($ownWorkspaces as $key => $value) {

            if(!in_array($value->id, $arr)){
                array_push($Workspaces, $value);
                array_push($arr, $value->id);
            }else{
                continue;
            }
        }

        return $Workspaces;
    }

    public function allTeamForUserCurrentWorkspace()
    {
        $workspace = $this->model->findOrFail(Auth::user()->current_workspace_id);
        $teams = [];
        foreach($workspace->teams as $team){
            if ($team->allUsers()->contains(auth()->id())) {
                $teams[] = $team;
            }
        }
        return $teams;
    }

}
