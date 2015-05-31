<?php namespace App\Repositories\Project;

use Illuminate\Database\Eloquent\Model;

use DB;

class EloquentProject implements ProjectInterface {

	protected $project;

	protected $maxDeployment;

	/**
	 * Create a new repository instance.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $project
	 * @param \Illuminate\Database\Eloquent\Model $maxDeployment
	 * @return void
	 */
	public function __construct(Model $project, Model $maxDeployment)
	{
		$this->project = $project;
		$this->maxDeployment = $maxDeployment;
	}

	/**
	 * Get a project by id.
	 *
	 * @param int $id Project id
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function byId($id)
	{
		return $this->project->find($id);
	}

	/**
	 * Get paginated projects.
	 *
	 * @param int $page  Page number
	 * @param int $limit Number of projects per page
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function byPage($page = 1, $limit = 10)
	{
		$projects = $this->project->orderBy('name')
			->skip($limit * ($page - 1))
			->take($limit)
			->paginate($limit);

		return $projects;
	}

	/**
	 * Create a new project.
	 *
	 * @param array $data Data to create a project
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $data)
	{
		$project = DB::transaction(function () use ($data)
		{
			$project = $this->project->create($data);

			$this->maxDeployment->project_id = $project->id;
			$this->maxDeployment->save();

			return $project;
		});

		return $project;
	}

	/**
	 * Update an existing project.
	 *
	 * @param array $data Data to update a project
	 * @return boolean
	 */
	public function update(array $data)
	{
		$project = $this->project->find($data['id']);

		$project->update($data);

		return true;
	}

	/**
	 * Delete an existing project.
	 *
	 * @param int $id Project id
	 * @return boolean
	 */
	public function delete($id)
	{
		$project = $this->project->find($id);

		$project->delete();

		return true;
	}

}
