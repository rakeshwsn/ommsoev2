public function edit()
	{
		              	
		$enterprisesmodel = new EnterprisesModel();
		if ($this->request->getMethod(1) == 'POST') {

			$district_id = $this->request->getGet('district_id');
			$block_id = $this->request->getGet('block_id');
				$enterprisesdata[] = [
					'unit_id' => $this->request->getPost('unit_id'),
					'district_id' => $this->request->getPost('unit_id'),
					'block_id' => $this->request->getPost('block_id'),
					'gp_id' => $this->request->getPost('gp_id'),
					'village' => $this->request->getPost('village'),
					'budget_fin_yr' => $this->request->getPost('budget_fin_yr'),
					'management_unit_type' => $this->request->getPost('management_unit_type'),
					'managing_unit_name' => $this->request->getPost('managing_unit_name'),
					'contact_person' => $this->request->getPost('contact_person'),
					'contact_mobile' => $this->request->getPost('contact_mobile'),
					'date_estd' => $this->request->getPost('date_estd'),
					'mou_date' => $this->request->getPost('mou_date'),
					'unit_budget_id' => $this->request->getPost('unit_budget_id'),
					'unit_budget_amount' => $this->request->getPost('unit_budget_amount'),
					'is_support_basis_infr' => $this->request->getPost('is_support_basis_infr'),
					'purpose_infr_support' => $this->request->getPost('purpose_infr_support'),
					'support_infr_budget_id' => $this->request->getPost('unit_budget_amount'),
					'support_infr_amount' => $this->request->getPost('support_infr_amount')
				];
		
			$data['enterprises']=$enterprisesmodel->insertBatch($enterprisesdata);
			
			return redirect()->to(admin_url('enterprises'))->with('message', 'successful');
		}
		

		return $this->getForm();
	}