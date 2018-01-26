<?php

namespace DivArt\Avatar;

use File;
use DivArt\Avatar\Exceptions\AvatarException;

class Avatar
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $backgroundColor;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $catalog;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $chars;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $driver;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $fontColor;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $fontFamily;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $fontSize;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $format;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $quality;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $radius;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $shape;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $type;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $text;

    /**
     * @var string
     */
    public $userInitials;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $width;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    public $height;

    /**
     * Avatar constructor.
     */
    public function __construct()
    {
        $this->catalog = config('avatar.catalog');
        $this->chars = config('avatar.chars');
        $this->backgroundIndex = array_rand(config('avatar.backgroundColor'));
        $this->backgroundColor = config('avatar.backgroundColor.' . $this->backgroundIndex);
        $this->driver = config('avatar.driver');
        $this->fontColor = config('avatar.fontColor');
        $this->fontFamily = config('avatar.fontFamily');
        $this->fontSize = config('avatar.fontSize');
        $this->format = config('avatar.format');
        $this->quality = config('avatar.quality');
        $this->radius = config('avatar.radius');
        $this->shape = config('avatar.shape');
        $this->type = config('avatar.type');
        $this->text = config('avatar.text');
        $this->width = config('avatar.width');
        $this->height = config('avatar.height');
    }

    /**
     * Accepts the user name
     *
     * @param $name
     * @return $this
     */
    public function make($name)
    {
        $this->name = $name;
        $this->userInitials($name);
        return $this;
    }

    /**
     * Set the font color
     *
     * @param $fontColor
     * @return $this
     */
    public function fontColor($fontColor)
    {
        $this->fontColor = $fontColor;
        return $this;
    }

    /**
     * Set the font family
     *
     * @param $fontFamily
     * @return $this
     */
    public function fontFamily($fontFamily)
    {
        $this->fontFamily = $fontFamily;
        return $this;
    }

    /**
     * Set the font size
     *
     * @param $fontSize
     * @return $this
     */
    public function fontSize($fontSize)
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * Set the format
     *
     * @param $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Set the radius
     *
     * @param $radius
     * @return $this
     */
    public function radius($radius)
    {
        $this->radius = $radius;
        return $this;
    }

    /**
     * Choose a shape
     *
     * @param null $shape
     * @return $this
     */
    public function shape($shape = null) 
    {
        $shape == null ? $this->shape = config('avatar.shape') : $this->shape = $shape;
        return $this;
    }

    /**
     * Set the size
     *
     * @param $width
     * @param null $height
     * @return $this
     */
    public function size($width, $height = null)
    {
        $this->width = $width;
        $height == null ? $this->height = $width : $this->height = $height;
        return $this;
    }

    /**
     * Call a function for shape create
     *
     * @param null $fileName
     */
    public function save($fileName = null)
    {
        $this->createShape($fileName);
    }

    /**
     * Create shape
     *
     * @param $fileName
     */
    public function createShape($fileName)
    {
        $im = imagecreatetruecolor($this->width, $this->height);

        $red = 100; 
        $green = 100; 
        $blue = 100;

        $backgroundColor = $this->backgroundColor;

        if(preg_match("|[#]?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})|", $backgroundColor, $ret)) { 
            $red = hexdec($ret[1]); 
            $green = hexdec($ret[2]); 
            $blue = hexdec($ret[3]); 
        }

        $color = imagecolorallocate($im, $red, $green, $blue);

        $fontColor = $this->fontColor;

        if(preg_match("|[#]?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})|", $fontColor, $ret)) { 
            $red = hexdec($ret[1]); 
            $green = hexdec($ret[2]); 
            $blue = hexdec($ret[3]); 
        }

        $black = imagecolorallocate($im, 0, 0, 0);

        imagecolortransparent($im, $black);

        switch($this->shape) {
            case 'square':
                imagefilledrectangle($im, 0, 0, $this->width, $this->height, $color);
                break;
            case 'circle':
                imagefilledellipse($im, ($this->width / 2), ($this->height / 2), ($this->width - 1), ($this->height - 1), $color);
                break;
            case 'rounded':
                $x1 = 0;
                $x2 = $this->width;
                $y1 = 0;
                $y2 = $this->height;

                $radius = $this->radiusInPercent($this->radius, $this->width);

                imagefilledrectangle($im, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
                imagefilledrectangle($im, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        
                $dia = $radius * 2;
        
                imagefilledellipse($im, $x1 + $radius, $y1 + $radius, $radius * 2, $dia, $color);
                imagefilledellipse($im, $x1 + $radius, $y2 - $radius, $radius * 2, $dia, $color);
                imagefilledellipse($im, $x2 - $radius, $y2 - $radius, $radius * 2, $dia, $color);
                imagefilledellipse($im, $x2 - $radius, $y1 + $radius, $radius * 2, $dia, $color);
                break;
            default:
                throw new AvatarException('Unknown shape');
                break;
        }

        $bbox = imagettfbbox($this->fontSize, 0, "fonts/$this->fontFamily.ttf", $this->userInitials);
        $bbox[0] = 0;
        $x = ($this->width / 2) - (($bbox[2] - $bbox[0]) / 2);

        $y = ($this->height / 2) - (($bbox[7] - $bbox[1]) / 2);

        imagettftext($im, $this->fontSize, 0, $x, $y, imagecolorallocate($im, $red, $green, $blue), "fonts/$this->fontFamily.ttf", $this->userInitials);

        if ( ! file_exists($this->catalog)) {
            mkdir($this->catalog, 0775, true);
        }

        $fileName == null ?  $fileName = $this->randomName($this->name) : $fileName = $this->catalog . "/$fileName." . $this->format;

        switch ($this->format) {
            case 'png':
                imagepng($im, $fileName);
                break;
            case 'jpg':
                imagejpeg($im, $fileName, $this->quality);
                break;
            case 'gif':
                imagegif($im, $fileName);
                break;
            default:
                throw new AvatarException('Unknown format');
                break;
        }

        imagedestroy($im);
    }

    /**
     * Return radius in percent
     *
     * @param $radius
     * @param $width
     * @return float
     */
    public function radiusInPercent($radius, $width)
    {
        return round($radius * $width / 100);
    }

    /**
     * Return user initials
     *
     * @param $userName
     * @return string
     */
    public function userInitials($userName)
    {
        if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {
            $initials = stristr($userName, '@', true);
            if (strpos($initials, '.')) {
                $initials = explode('.', $initials);
            } else if (strpos($initials, '-')) {
                $initials = explode('-', $initials);
            } else if (strpos($initials, '_')) {
                $initials = explode('_', $initials);
            }
            if (count($initials) == 1) {
                switch ($this->chars) {
                    case 1:
                        $this->userInitials .= strtoupper(substr($initials, 0, 1));
                        break;
                    case 2:
                        $this->userInitials .= strtoupper(substr($initials, 0, 2));
                        break;
                    case 3:
                        $this->userInitials .= strtoupper(substr($initials, 0, 3));
                        break;
                }
                return $this->userInitials;
            } else if (count($initials) >= 2 && $this->chars == 2 || count($initials) >= 2 && $this->chars == 1) {
                foreach ($initials as $key => $initial) {
                    if ($key >= $this->chars || $key >= 3) {
                        break;
                    }
                    $this->userInitials .= strtoupper(substr($initial, 0, 1));
                }

                return $this->userInitials;
            } else if ($this->chars == 3) {
                $i = 2;
                foreach ($initials as $key => $initial) {
                    if ($key >= $this->chars || $key >= 3) {
                        break;
                    }
                    $this->userInitials .= strtoupper(substr($initial, 0, $i));

                    $i--;
                }
            }
        } else {
            $initials = explode(' ', $userName);

            if (count($initials) <= 1) {
                switch ($this->chars) {
                    case 1:
                        $this->userInitials .= strtoupper(substr($initials[0], 0, 1));
                        break;
                    case 2:
                        $this->userInitials .= strtoupper(substr($initials[0], 0, 2));
                        break;
                    case 3:
                        $this->userInitials .= strtoupper(substr($initials[0], 0, 3));
                        break;
                }
                
                return $this->userInitials;
            } else if (count($initials) >= 2 && $this->chars == 2 || count($initials) >= 2 && $this->chars == 1) {
                foreach ($initials as $key => $initial) {
                    if ($key >= $this->chars || $key >= 3) {
                        break;
                    }
                    $this->userInitials .= strtoupper(substr($initial, 0, 1));
                }

                return $this->userInitials;
            } else if ($this->chars == 3) {
                $i = 2;
                foreach ($initials as $key => $initial) {
                    if ($key >= $this->chars || $key >= 3) {
                        break;
                    }
                    $this->userInitials .= strtoupper(substr($initial, 0, $i));

                    $i--;
                }
            }
        } 
    }

    /**
     * Return random name for avatar
     *
     * @param $name
     * @return string
     */
    public function randomName($name)
    {
        $num = rand(0, 1000);

        $fileName = hash('crc32', $num . $name);

        return $this->catalog . "/$fileName." . $this->format;
    }
}