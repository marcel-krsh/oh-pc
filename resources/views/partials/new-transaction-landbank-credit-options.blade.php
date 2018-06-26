<label class="uk-form-label" for="credit_option_id">{{$option_title}}s</label>
                        <div class="uk-form-controls">
                            <div>
							    <select name="credit_option_id" id="credit_option"  class="uk-select">
	                                <option value="">Select {{$option_title}}</option>
	                            	@foreach($options as $option)
	                                <option value="{{$option->id}}" >{{$option_title}} #{{$option->id}} | {{date('m/j/Y',strtotime($option->created_at))}}</option>
									@endforeach
	                            </select>
	                        </div>
                        </div>