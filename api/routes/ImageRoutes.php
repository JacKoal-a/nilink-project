<?php

Route::add(IMAGE.ALL, function($imgId) {
    header('Content-type: image/png');
    try{
        require 'uploads/default.png';
    }catch(Exception $e){
        return handleException($e);
    }
    
  }, "GET");


Route::add(ICON.ALL, function($imgId) {
    
    $path='uploads/icons/'.$imgId.'.png';
    try{
        if(file_exists($path)){
            header('Content-type: image/png');
            require $path;
        }else{
            throw new NotFoundException("Icon with id '".$imgId."' doesn't exist");
        }
        
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "GET");

Route::add(SCREENSHOT.ALL, function($imgId) {
    
    $path='uploads/screenshots/'.$imgId.'.png';
    try{
        if(file_exists($path)){
            header('Content-type: image/png');
            require $path;
        }else{
            throw new NotFoundException("Screenshot with id '".$imgId."' doesn't exist");
        }
        
    }catch(Exception $e){
        return handleException($e);
    }
    
}, "GET");
  
  
