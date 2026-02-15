@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.SEO settings')
@endsection

@section('css')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bx bx-search-alt-2"></i> @lang('l.SEO settings')</h5>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('l.Slug')</th>
                                        <th>@lang('l.Title')</th>
                                        <th>@lang('l.Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($seoPages as $page)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><code>{{ $page->slug }}</code></td>
                                        <td>{{ Str::limit($page->title, 30) }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('dashboard.admins.seo-show', ['id' => encrypt($page->id)]) }}" class="btn btn-sm btn-info">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('dashboard.admins.seo-edit', ['id' => encrypt($page->id)]) }}" class="btn btn-sm btn-warning">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <a href="{{ route('dashboard.admins.seo-get-translations', ['id' => encrypt($page->id)]) }}" class="btn btn-sm btn-dark">
                                                    <i class="bx bx-globe"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">@lang('l.No SEO pages found')</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection