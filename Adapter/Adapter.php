<?php


namespace Application\Adapter;


interface Adapter
{
	public function get(Request $request): array;
}