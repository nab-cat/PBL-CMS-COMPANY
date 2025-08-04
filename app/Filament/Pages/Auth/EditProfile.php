<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
	public function form(Form $form): Form
	{
		return $form
			->schema([
				$this->getNameFormComponent(),
				$this->getEmailFormComponent()
					->disabled(),
				FileUpload::make('foto_profil')
					->label('Foto profil')
					->image()
					->disk('public')
					->directory('profile-photos')
					->avatar()
					->maxSize(1024),
				$this->getPasswordFormComponent(),
				$this->getPasswordConfirmationFormComponent(),
			]);
	}
}
