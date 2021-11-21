<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\Models\Laporan;
use App\Models\Pelanggaran;
use App\Models\BentukPelanggaran;
use App\Models\Kawasan;
use App\Models\Kota;
use Carbon\Carbon;
use Auth;

class ChartController extends Controller
{
    use Authorizable;

    public function index(Request $request)
    {
        $user = Auth::user();
        $roles = $user->getRoleNames();
        $user_city = $user->city_id ? Helper::GetOperatorCityName($user->city->city_name) : '';
        $city = Kota::all()->pluck('city_name', 'city_name');

        $month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $oldest = Laporan::select('created_at')->oldest()->first();
        $latest = Laporan::select('created_at')->latest()->first();
        $year_range = Carbon::parse($oldest->created_at)->format('Y') . "-" . Carbon::parse($latest->created_at)->format('Y');

        $pelanggarans = Pelanggaran::select('uuid', 'name')->get();
        $bentuks = BentukPelanggaran::select('uuid', 'bentuk_pelanggaran')->get();
        $kawasans = Kawasan::select('uuid', 'kawasan')->get();

        $jenis_series = [];
        $jenis_count = [];
        $bentuk_series = [];
        $bentuk_count = [];
        $bentuk_drilldown = [];
        $kawasan_series = [];
        $kawasan_count = [];
        $kawasan_drilldown = [];

        foreach ($pelanggarans as $pelanggaran) {
            $laporan_jenis = Laporan::select('uuid', 'jenis_pelanggaran', 'created_at')
                ->where('jenis_pelanggaran', $pelanggaran->uuid)
                ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                    return $query->where(function ($q) use ($user, $user_city) {
                        return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                    });
                })
                ->get()
                ->groupBy(function ($date) {
                    return (int)Carbon::parse($date->created_at)->format('m');
                });

            for ($j = 1; $j <= 12; $j++) {
                if (!empty($laporan_jenis[$j])) {
                    $jenis_count[$j]['count'] = count($laporan_jenis[$j]);
                } else {
                    $jenis_count[$j]['count'] = 0;
                }
            }
            $collection = collect($jenis_count);
            $flattened = $collection->flatten();
            $flattened->all();
            $jenis_series[] = array(
                "name" => $pelanggaran->name,
                "data" => $flattened,
            );
        }

        foreach ($bentuks as $bentuk) {
            $laporan_bentuk = Laporan::select('uuid', 'bentuk_pelanggaran', 'created_at')
                ->where('bentuk_pelanggaran', $bentuk->uuid)
                ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                    return $query->where(function ($q) use ($user, $user_city) {
                        return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                    });
                })
                ->get()
                ->groupBy('bentuk_pelanggaran');

            foreach ($laporan_bentuk as $k => $v) {
                $bentuk_series[] = array(
                    "name" => $bentuk->bentuk_pelanggaran,
                    "y" => count($v),
                    "drilldown" => $bentuk->bentuk_pelanggaran
                );
            }
            $laporan_bentuk_monthly = Laporan::select('uuid', 'bentuk_pelanggaran', 'created_at')
                ->where('bentuk_pelanggaran', $bentuk->uuid)
                ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                    return $query->where(function ($q) use ($user, $user_city) {
                        return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                    });
                })
                ->get()
                ->groupBy(function ($date) {
                    return (int)Carbon::parse($date->created_at)->format('m');
                });
            for ($j = 1; $j <= 12; $j++) {
                if (!empty($laporan_bentuk_monthly[$j])) {
                    $bentuk_count[$j] = array(
                        $month[$j - 1],
                        count($laporan_bentuk_monthly[$j]),
                    );
                } else {
                    $bentuk_count[$j] = array(
                        $month[$j - 1],
                        0,
                    );
                }
            }
            $bentuk_drilldown[] = array(
                "name" => $bentuk->bentuk_pelanggaran,
                "id" => $bentuk->bentuk_pelanggaran,
                "data" => $bentuk_count,
            );
        }

        foreach ($kawasans as $kawasan) {
            $laporan_kawasan = Laporan::select('uuid', 'kawasan', 'created_at')
                ->where('kawasan', $kawasan->uuid)
                ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                    return $query->where(function ($q) use ($user, $user_city) {
                        return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                    });
                })
                ->get()
                ->groupBy('kawasan');

            foreach ($laporan_kawasan as $k => $v) {
                $kawasan_series[] = array(
                    "name" => $kawasan->kawasan,
                    "y" => count($v),
                    "drilldown" => $kawasan->kawasan
                );
            }
            $laporan_kawasan_monthly = Laporan::select('uuid', 'kawasan', 'created_at')
                ->where('kawasan', $kawasan->uuid)
                ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                    return $query->where(function ($q) use ($user, $user_city) {
                        return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                    });
                })
                ->get()
                ->groupBy(function ($date) {
                    return (int)Carbon::parse($date->created_at)->format('m');
                });
            for ($j = 1; $j <= 12; $j++) {
                if (!empty($laporan_kawasan_monthly[$j])) {
                    $kawasan_count[$j] = array(
                        $month[$j - 1],
                        count($laporan_kawasan_monthly[$j]),
                    );
                } else {
                    $kawasan_count[$j] = array(
                        $month[$j - 1],
                        0,
                    );
                }
            }
            $kawasan_drilldown[] = array(
                "name" => $kawasan->kawasan,
                "id" => $kawasan->kawasan,
                "data" => $kawasan_count,
            );
        }
        return view('chart.index', compact('month', 'city', 'year_range', 'jenis_series', 'bentuk_series', 'bentuk_drilldown', 'kawasan_series', 'kawasan_drilldown'));
    }

    public function filter(Request $request)
    {
        $user = Auth::user();
        $roles = $user->getRoleNames();
        $user_city = $user->city_id ? Helper::GetOperatorCityName($user->city->city_name) : '';
        $request_city = Helper::GetOperatorCityName($request->get('city'));
        $month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        if (request()->ajax()) {
            $jenis_series = [];
            $jenis_count = [];
            $bentuk_series = [];
            $bentuk_count = [];
            $bentuk_drilldown = [];
            $kawasan_series = [];
            $kawasan_count = [];
            $kawasan_drilldown = [];
            $pelanggarans = Pelanggaran::select('uuid', 'name')->get();
            $bentuks = BentukPelanggaran::select('uuid', 'bentuk_pelanggaran')->get();
            $kawasans = Kawasan::select('uuid', 'kawasan')->get();

            foreach ($pelanggarans as $pelanggaran) {
                $laporan_jenis = Laporan::select('uuid', 'jenis_pelanggaran', 'created_at')
                    ->where('jenis_pelanggaran', $pelanggaran->uuid)
                    ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                        return $query->where(function ($q) use ($user, $user_city) {
                            return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                                ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                                ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                        });
                    })
                    ->when($request->get('year'), function ($query) use ($request) {
                        return $query->whereYear('created_at', $request->get('year'));
                    })
                    ->when($request->get('city'), function ($query) use ($request, $request_city) {
                        return $query->where(function ($q) use ($request, $request_city) {
                            return $q->where('kota', 'like', '%' . $request->get('city') . '%')
                                ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'City' . '%')
                                ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'Regency' . '%');
                        });
                    })
                    ->get()
                    ->groupBy(function ($date) {
                        return (int)Carbon::parse($date->created_at)->format('m');
                    });

                if (!empty($laporan_jenis)) {
                    for ($j = 1; $j <= 12; $j++) {
                        if (!empty($laporan_jenis[$j])) {
                            $jenis_count[$j]['count'] = count($laporan_jenis[$j]);
                        } else {
                            $jenis_count[$j]['count'] = 0;
                        }
                    }
                    $collection = collect($jenis_count);
                    $flattened = $collection->flatten();
                    $flattened->all();
                    $jenis_series[] = array(
                        "name" => $pelanggaran->name,
                        "data" => $flattened,
                    );
                }
            }

            foreach ($bentuks as $bentuk) {
                $laporan_bentuk = Laporan::select('uuid', 'bentuk_pelanggaran', 'created_at')
                    ->where('bentuk_pelanggaran', $bentuk->uuid)
                    ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                        return $query->where(function ($q) use ($user, $user_city) {
                            return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                                ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                                ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                        });
                    })
                    ->when($request->get('year'), function ($query) use ($request) {
                        return $query->whereYear('created_at', $request->get('year'));
                    })
                    ->when($request->get('city'), function ($query) use ($request, $request_city) {
                        return $query->where(function ($q) use ($request, $request_city) {
                            return $q->where('kota', 'like', '%' . $request->get('city') . '%')
                                ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'City' . '%')
                                ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'Regency' . '%');
                        });
                    })
                    ->get()
                    ->groupBy('bentuk_pelanggaran');
                if (!empty($laporan_bentuk)) {
                    foreach ($laporan_bentuk as $k => $v) {
                        $bentuk_series[] = array(
                            "name" => $bentuk->bentuk_pelanggaran,
                            "y" => count($v),
                            "drilldown" => $bentuk->bentuk_pelanggaran
                        );
                    }
                    $laporan_bentuk_monthly = Laporan::select('uuid', 'bentuk_pelanggaran', 'created_at')
                        ->where('bentuk_pelanggaran', $bentuk->uuid)
                        ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                            return $query->where(function ($q) use ($user, $user_city) {
                                return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                                    ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                                    ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                            });
                        })
                        ->when($request->get('year'), function ($query) use ($request) {
                            $query->whereYear('created_at', $request->get('year'));
                        })
                        ->when($request->get('city'), function ($query) use ($request, $request_city) {
                            return $query->where(function ($q) use ($request, $request_city) {
                                return $q->where('kota', 'like', '%' . $request->get('city') . '%')
                                    ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'City' . '%')
                                    ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'Regency' . '%');
                            });
                        })
                        ->get()
                        ->groupBy(function ($date) {
                            return (int)Carbon::parse($date->created_at)->format('m');
                        });
                    for ($j = 1; $j <= 12; $j++) {
                        if (!empty($laporan_bentuk_monthly[$j])) {
                            $bentuk_count[$j] = array(
                                $month[$j - 1],
                                count($laporan_bentuk_monthly[$j]),
                            );
                        } else {
                            $bentuk_count[$j] = array(
                                $month[$j - 1],
                                0,
                            );
                        }
                    }
                    $bentuk_drilldown[] = array(
                        "name" => $bentuk->bentuk_pelanggaran,
                        "id" => $bentuk->bentuk_pelanggaran,
                        "data" => $bentuk_count,
                    );
                }
            }
            foreach ($kawasans as $kawasan) {
                $laporan_kawasan = Laporan::select('uuid', 'kawasan', 'created_at')
                    ->where('kawasan', $kawasan->uuid)
                    ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                        return $query->where(function ($q) use ($user, $user_city) {
                            return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                                ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                                ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                        });
                    })
                    ->when($request->get('year'), function ($query) use ($request) {
                        return $query->whereYear('created_at', $request->get('year'));
                    })
                    ->when($request->get('city'), function ($query) use ($request, $request_city) {
                        return $query->where(function ($q) use ($request, $request_city) {
                            return $q->where('kota', 'like', '%' . $request->get('city') . '%')
                                ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'City' . '%')
                                ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'Regency' . '%');
                        });
                    })
                    ->get()
                    ->groupBy('kawasan');

                if (!empty($laporan_kawasan)) {
                    foreach ($laporan_kawasan as $k => $v) {
                        $kawasan_series[] = array(
                            "name" => $kawasan->kawasan,
                            "y" => count($v),
                            "drilldown" => $kawasan->kawasan
                        );
                    }
                    $laporan_kawasan_monthly = Laporan::select('uuid', 'kawasan', 'created_at')
                        ->where('kawasan', $kawasan->uuid)
                        ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                            return $query->where(function ($q) use ($user, $user_city) {
                                return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                                    ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                                    ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                            });
                        })
                        ->when($request->get('year'), function ($query) use ($request) {
                            return $query->whereYear('created_at', $request->get('year'));
                        })
                        ->when($request->get('city'), function ($query) use ($request, $request_city) {
                            return $query->where(function ($q) use ($request, $request_city) {
                                return $q->where('kota', 'like', '%' . $request->get('city') . '%')
                                    ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'City' . '%')
                                    ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'Regency' . '%');
                            });
                        })
                        ->get()
                        ->groupBy(function ($date) {
                            return (int)Carbon::parse($date->created_at)->format('m');
                        });
                    for ($j = 1; $j <= 12; $j++) {
                        if (!empty($laporan_kawasan_monthly[$j])) {
                            $kawasan_count[$j] = array(
                                $month[$j - 1],
                                count($laporan_kawasan_monthly[$j]),
                            );
                        } else {
                            $kawasan_count[$j] = array(
                                $month[$j - 1],
                                0,
                            );
                        }
                    }
                    $kawasan_drilldown[] = array(
                        "name" => $kawasan->kawasan,
                        "id" => $kawasan->kawasan,
                        "data" => $kawasan_count,
                    );
                }
            }
            return response()->json([
                'jenis_series' => $jenis_series,
                'bentuk_series' => $bentuk_series,
                'bentuk_drilldown' => $bentuk_drilldown,
                'kawasan_series' => $kawasan_series,
                'kawasan_drilldown' => $kawasan_drilldown,
            ]);
        }
    }
}
