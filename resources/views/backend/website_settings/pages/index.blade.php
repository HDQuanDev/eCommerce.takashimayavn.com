@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col">
			<h1 class="h3">{{ translate('Website Pages') }}</h1>
		</div>
	</div>
</div>

<div class="card">
	@can('add_website_page')
		<div class="card-header">
			<h6 class="mb-0 fw-600">{{ translate('All Pages') }}</h6>
			<a href="{{ route('custom-pages.create') }}" class="btn btn-circle btn-info">{{ translate('Add New Page') }}</a>
		</div>
	@endcan
	<div class="card-body">
		<table class="lmt-table">
        <thead>
            <tr>
                <th data-breakpoints="lg">#</th>
                <th>{{translate('Name')}}</th>
                <th data-breakpoints="md">{{translate('URL')}}</th>
                <th class="text-right">{{translate('Actions')}}</th>
            </tr>
        </thead>
        <tbody>
        	@foreach ($page as $key => $page)
						<tr>
							<td data-text="#">{{ $key+1 }}</td>
							<td data-text="{{translate('Name')}}"><a href="{{ route('custom-pages.show_custom_page', $page->slug) }}" class="text-reset">{{ $page->getTranslation('title') }}</a></td>
							<td data-text="{{translate('URL')}}">{{ route('home') }}/{{ $page->slug }}</td>
							<td class="text-right" data-text="{{translate('Actions')}}">
								<div class="">
									@can('edit_website_page')
										@if($page->type == 'home_page')
											<a href="{{route('custom-pages.edit', ['id'=>$page->slug, 'lang'=>env('DEFAULT_LANGUAGE'), 'page'=>'home'] )}}" class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Edit">
												<i class="las la-pen"></i>
											</a>
										@else
											<a href="{{route('custom-pages.edit', ['id'=>$page->slug, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Edit">
												<i class="las la-pen"></i>
											</a>
										@endif
									@endcan
									@if($page->type == 'custom_page' && auth()->user()->can('delete_website_page'))
													<a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('custom-pages.destroy', $page->id)}} " title="{{ translate('Delete') }}">
														<i class="las la-trash"></i>
													</a>
									@endif
								</div>
							</td>
						</tr>
					@endforeach
        </tbody>
    </table>
	</div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
