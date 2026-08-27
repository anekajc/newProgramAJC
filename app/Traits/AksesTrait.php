<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\NewMenuReport;
use App\Models\NewPeriode;

use Carbon\Carbon;use Illuminate\Support\Arr;

trait AksesTrait {
	public function cekAkses($href) {
		// Sesi habis / user sudah logout: jangan sentuh Auth::user() (null),
		// cukup kembalikan flag supaya controller me-redirect ke halaman login.
		if (!\Auth::check()) {
			return array('userLoggedOut' => true);
		}

		$akses = array('userLoggedOut' => false);

    	// $periode = NewPeriode::where('USERID' , \Auth::User()->username)->first();
		$periode = collect(
		    DB::connection('SML')->select(
		        'select top 1 bulan, tahun from DBPERIODE where user_id = ?',
		        [\Auth::user()->username]
		    )
		)->first();
		$menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0Report(5);

		$program = DB::connection('SML')->select('select * from DBPERUSAHAAN');
		$akses = Arr::add($akses, 'program', $program[0]->NAMA);
		$akses = Arr::add($akses, 'href', $href);
		$akses = Arr::add($akses, 'user', \Auth::User()->username);
		
		if ($href != "Home") {
			$menu = NewMenuReport::where('href' , $href)->first();
			$aksesmenu = $this->getAksesMenu($href);
			$akses = Arr::add($akses, 'akses', $aksesmenu[0]);
			$akses = Arr::add($akses, 'namamenu', $menu->Keterangan);

			// Dikirim bersama halaman supaya doLoadHeader() di masterreport2/2x/Gudang
			// tidak perlu lagi AJAX sinkron ke globalfunctions_doLoadHeader saat page
			// load (dulu terjadi 2x per halaman -- sekali dari ready halaman, sekali
			// lagi dari ready master layout -- masing-masing mengunci main thread).
			$simpanheader = DB::connection('SML')->select(
				'select reportmode, header, issubtotal, isgrandtotal from DBSIMPANHEADER where username = ? and href = ?',
				[\Auth::user()->username, $href]
			);
		} else {
			$akses = Arr::add($akses, 'namamenu', "Home");
			$simpanheader = [];
		}

		$akses = Arr::add($akses, 'simpanheader', $simpanheader);
		$akses = Arr::add($akses, 'periode', $periode);
		$akses = Arr::add($akses, 'menul0' , $menul0);

		/* // Tidak jadi dipakai karena timestamp tidak akurat
		$datetime = Carbon::now()->toDateTimeString();            // yyyy-mmm-dd hh:mm
		$datetime = Str::replaceArray('-', ['', ''], $datetime);  // yyyymmmdd hh:mm:ss
		$datetime = Str::replaceArray(':', ['', ''], $datetime);  // yyyymmmdd hhmmss
		$datetime = Str::replaceArray(' ', ['_'], $datetime);     // yyyymmmdd_hhmmss
		$akses = Arr::add($akses, 'xlsfilename', strtoupper($href) . "_" . $datetime);
		*/
		$akses = Arr::add($akses, 'xlsfilename', strtoupper($href));

		return $akses;
	}

	public function getAksesMenu($href) {
		if (!\Auth::check()) {
			return array();
		}

		return DB::connection('SML')->select('select fl.* from DBFLMENUREPORT fl left outer join DBMENUREPORT m on (fl.L1 = m.KODEMENU) where fl.UserID = :user and m.href = :href' , ['user' => \Auth::User()->username, 'href' => $href]);
	}


	/*
	// 2 function di bawah adalah function dari program Project
	// ditaruh sini sebgai refrensi
	public function checkMenu($access, $otherColumn = '') {
		$checkStatusUser = User::where('id', \Auth::id())->first();
		if ($checkStatusUser->status == 0) {
			User::where('id', Auth::id())->update(['status' => 0, 'hostid' => '', 'ipaddress' => '']);
			Auth::logout();
			$akses = array('userLoggedOut' => true);
			return $akses;
		}
		
		$columnName = ($otherColumn != '') ? $otherColumn : 'access';

		$getKode = Menu::where($columnName, $access)->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('kode_menu', $getKode->kode)->first();

		$akses = array('userLoggedOut' => false);
		$akses = Arr::add($akses, 'akses', $check);

		if ($check->tampil == 1) {
			User::where('id', \Auth::id())
				->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);

			$checkUser = User::where('id', \Auth::id())->first();
			if ($checkUser->level == 3 && $checkUser->username == 'SA') {
	    		$menu = Menu::join('akses_menu', 'menu.kode', '=', 'akses_menu.kode_menu')
	    			->where('akses_menu.tampil', 1)
	    			->where('menu.show_acc', 1)
	    			->where('akses_menu.id_user', \Auth::id())
	    			->select('menu.*')->orderBy('kode', 'ASC')
	    			->get();
	  		} else {
	    		$menu = Menu::join('akses_menu', 'menu.kode', '=', 'akses_menu.kode_menu')
	    			->where('akses_menu.tampil', 1)
	    			->where('menu.grup','>', 0)
	    			->where('menu.show_acc', 1)
	    			->where('akses_menu.id_user', \Auth::id())
	    			->select('menu.*')->orderBy('kode', 'ASC')
	    			->get();
	  		}
	  		
			$periode = Periode::where('id_user', \Auth::id())->first();

			$akses = Arr::add($akses, 'allowTransEdit', $this->allowTransEdit($checkUser->level));
		} else {
			return $akses;
		}

		$akses = Arr::add($akses, 'menu', $menu);
		$akses = Arr::add($akses, 'periode', $periode);
		return $akses;
	}

	public function allowTransEdit($level) {
		if ($level == 3) {
			$getKode = Menu::where('access', '/transaksidiedit')->first();
			$check = AksesMenu::where('id_user', \Auth::id())->where('kode_menu', $getKode->kode)->first();

			if ($check->tampil == 1) { return true; }
		}

		return false;
	}
	/* */
  
}
