<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use PhpOffice\PhpPresentation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mnvx\Lowrapper;
use App;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }
    
    function is_image($path)
    {
        $image_type = [];
        
        try {
            $image_type = getimagesize($path)[2];
        } catch (ErrorException $e) {
            return false;
        }
    
        if (in_array($image_type , array(IMAGETYPE_GIF, IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
            return true;

        return false;
    }
    
    public function processUpload(Request $request)
    {
        if ($request->file('file')->getClientOriginalExtension() == 'pptx') {   
            return response()->json([
                'images' => $this->processPptxRequest($request),
                'filename' =>  md5_file($request->file('file')),
            ]);
        } elseif ($request->file('file')->getClientOriginalExtension() == 'ppt') {
            return response()->json([
                'images' => $this->processPptFile($request),
                'filename' =>  md5_file($request->file('file')),
            ]);
        }
    }
    
    /**
     * Take the request, get the pptx out of the request, convert to zip and return the images in the zip.
     */
    public function processPptxRequest(Request $request) : array
    {
        // File is PPTX, save as Zip
        $fileHash = md5_file($request->file('file'));
        $fileName = $fileHash . '.zip';
        $request->file('file')->storeAs('powerpoints', $fileName);
        
        $pptxReader = PhpPresentation\IOFactory::createReader('PowerPoint2007');
        $pres = $pptxReader->load(storage_path() . '/app/powerpoints/' . $fileName);
        $template = $pptxReader->load(storage_path() . '/app/powerpoints/templates/Bronvermelding.pptx');
        
        for($i=0; $i < $template->getSlideCount(); $i++)
            $pres->addExternalSlide($template->getSlide($i));
        
        $writer = PhpPresentation\IOFactory::createWriter($pres, 'PowerPoint2007');
        $writer->save(storage_path() . '/app/powerpoints/phpPres/' . $fileName);
        
        $archive = new ZipArchive;
        $extractPath = 'storage/app/powerpoints/extracted/' . $fileName;
        if ($archive->open($request->file('file')) === true)
        {
            $archive->extractTo($extractPath);
            $archive->close();
        }
        
        $files = (file_exists($extractPath . '/ppt/media')) ? preg_grep('/^([^.])/', scandir($extractPath . '/ppt/media/')) : [];
        $images = [];
        
        $presentation = App\Presentation::where('hash', $fileHash)->first();
        if ($presentation !== null) $presId = $presentation->id;
        
        foreach($files as $file)
        {
            if($this->is_image('storage/app/powerpoints/extracted/' . $fileName . '/ppt/media/' . $file))
            {
                $imageHash = md5_file('storage/app/powerpoints/extracted/' . $fileName . '/ppt/media/' . $file);
                
                $imageArray = [
                    'path' => 'storage/app/powerpoints/extracted/' . $fileName . '/ppt/media/' . $file,
                    'file' => $imageHash
                ];
                
                if(isset($presId)) {
                    $image = App\Image::where('presentation_id', '=', $presId)
                                    ->where('hash', '=', $imageHash)
                                    ->first();
                                    
                    if($image !== null) {
                        $imageArray['source'] = $image->source;
                    }
                }
                
                $images[] = $imageArray;
            }
        }
        return $images;
    }
    
    /**
     * Take the PPT File, convert to PPTX, then to zip and return the images in the zip.
     */
    public function processPptFile(Request $request) : array
    {
        $fileHash = md5_file($request->file('file'));
        $fileName = md5_file($request->file('file'));
        // Save file as PPT
        $request->file('file')->storeAs('powerpoints/pptHashed/', $fileName . '.ppt');
        // Create PPT reader
        
        $converter = new Lowrapper\Converter;
        $parameters = (new Lowrapper\LowrapperParameters)
            ->setInputFile(storage_path() . '/app/powerpoints/pptHashed/' . $fileName . '.ppt')
            ->setOutputFormat(Lowrapper\Format::PRESENTATION_PPTX)
            ->setOutputFile(storage_path() . '/app/powerpoints/' . $fileName . '.zip');
        $converter->convert($parameters);
        
        // File is PPTX, save as Zip
        
        $archive = new ZipArchive;
        $extractPath = storage_path() . '/app/powerpoints/extracted/' . $fileName;
        if ($archive->open(storage_path() . '/app/powerpoints/' . $fileName . '.zip') === true)
        {
            $archive->extractTo($extractPath);
            $archive->close();
        }
        
        $files = (file_exists($extractPath . '/ppt/media')) ? preg_grep('/^([^.])/', scandir($extractPath . '/ppt/media/')) : [];
        $images = [];
        
        $presentation = App\Presentation::where('hash', $fileHash)->first();
        if ($presentation !== null) $presId = $presentation->id;
        
        foreach($files as $file)
        {
            if($this->is_image('storage/app/powerpoints/extracted/' . $fileName . '/ppt/media/' . $file))
            {
                $imageHash = md5_file('storage/app/powerpoints/extracted/' . $fileName . '/ppt/media/' . $file);
                
                $imageArray = [
                    'path' => 'storage/app/powerpoints/extracted/' . $fileName . '/ppt/media/' . $file,
                    'file' => $imageHash
                ];
                
                if(isset($presId)) {
                    $image = App\Image::where('presentation_id', '=', $presId)
                                    ->where('hash', '=', $imageHash)
                                    ->first();
                                    
                    if($image !== null) {
                        $imageArray['source'] = $image->source;
                    }
                }
                
                $images[] = $imageArray;
            }
        }
        return $images;
    }
    
    public function postForm(Request $request)
    {
        $requestData = $request->all();
        unset($requestData['_token']);
        // Filename 
        $presentationHash = $requestData['filehash'];
        unset($requestData['filehash']);
        
        $presentation = App\Presentation::where('hash', $presentationHash)->first();

        if($presentation === null) {
            $presentation = App\Presentation::create(['hash' => $presentationHash]);
            $presentation->save();
        }
        
        $presId = App\Presentation::find($presentation->id)->id;
        
        $images = [];
        foreach($requestData as $hash => $source) {
            if ($source === null) continue;
            
            $image = App\Image::where('presentation_id', '=', $presId)
                                ->where('hash', '=', $hash)
                                ->first();
            if ($image === null) {
                $image = App\Image::create([
                                            'hash' => $hash,
                                            'presentation_id' => $presId,
                                            'source' => $source
                                            ]);
                $images[] = $image;
                $image->save();
            }
        }
        
        return redirect('/');
    }
}
