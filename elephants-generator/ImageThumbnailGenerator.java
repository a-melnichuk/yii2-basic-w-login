package elephantsgenerator;

import java.awt.Image;
import java.awt.image.BufferedImage;
import java.io.File;
import javax.imageio.ImageIO;


public class ImageThumbnailGenerator {
    int maxImageWidth,
          maxImageHeight,
          maxThumbnailWidth,
          maxThumbnailHeight;
    
    String imagesDir,
           thumbnailsDir;
    
    public ImageThumbnailGenerator(int maxImageWidth,int maxImageHeight,
                                   int maxThumbnailWidth,int maxThumbnailHeight,
                                   String imagesDir,String thumbnailsDir) {
        
        this.maxImageWidth= maxImageWidth;
        this.maxImageHeight = maxImageHeight;
        this.maxThumbnailWidth = maxThumbnailWidth;
        this.maxThumbnailHeight = maxThumbnailWidth;
        this.imagesDir = imagesDir;
        this.thumbnailsDir = thumbnailsDir;
        
    }
    //rescale image to new size and write it to folder selected
    private void writeScaledImg(File imgFile,String dest,int newWidth,int newHeight){
        String imgName = imgFile.getName();
        try{
            BufferedImage img = new BufferedImage(newWidth, newHeight, BufferedImage.TYPE_INT_RGB);
            img.createGraphics().drawImage(ImageIO.read(imgFile).getScaledInstance(newWidth, newHeight, Image.SCALE_SMOOTH),0,0,null);
            
            ImageIO.write(img, "jpg", new File(dest+"\\"+imgName));
        }catch(Exception e){
            //delete generated image ,if io fails
            e.printStackTrace();
            new File(dest+"\\"+imgName).delete();
        }
    }
    
    //rescale image by given width,solve for height
    private void writeScaledByWidthImage(File imgFile,String dest,int newWidth){
        try{
            BufferedImage bimg = ImageIO.read(imgFile);
            int width = bimg.getWidth();
            int height = bimg.getHeight();
            if(newWidth >= width){ 
                writeScaledImg(imgFile, dest, width, height);
                return;
            }
            
            float ratio = ((float)height)/((float)width);
            int newHeight = Math.round(newWidth*ratio);
            writeScaledImg(imgFile, dest, newWidth, newHeight);
        }catch(Exception e){
            e.printStackTrace();
        }
    }
    
    //rescale image by given height,solve for width
    private void writeScaledByHeightImage(File imgFile,String dest,int newHeight){
        try{
            BufferedImage bimg = ImageIO.read(imgFile);
            int height = bimg.getHeight();
            int width = bimg.getWidth();
            if(newHeight >=height){
                writeScaledImg(imgFile, dest, width, height);
                return;
            } 
            float ratio = height/width;
            int newWidth = Math.round(newHeight/ratio);
            writeScaledImg(imgFile, dest, width, newHeight);
        }catch(Exception e){

        }
    }
    
    
    public void createThumbnailByWidth(File imgFile){
        this.writeScaledByWidthImage(imgFile, thumbnailsDir, maxThumbnailWidth);
    }
    
    public void createImageByWidth(File imgFile){
        this.writeScaledByWidthImage(imgFile, imagesDir, maxImageWidth);
    }
   
    public void createThumbnailByHeight(File imgFile){
        this.writeScaledByHeightImage(imgFile, thumbnailsDir, maxThumbnailHeight);
    }
    
    public void createImageByHeight(File imgFile){
        this.writeScaledByHeightImage(imgFile, imagesDir, maxImageHeight);
    }
    
    
    
    
    
}
