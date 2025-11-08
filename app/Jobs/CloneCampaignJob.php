<?php
namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\GenerateMockupJob;

class CloneCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $campaignId;
    protected array $campaignData;

    public function __construct(string $campaignId, array $campaignData)
    {
        $this->campaignId = $campaignId;
        $this->campaignData = $campaignData;
    }

    public function handle(): void
    {
        $originalCampaign = Campaign::with(['products.productSides', 'products.variations', 'products.colors'])
            ->findOrFail($this->campaignId);

        $newCampaign = $originalCampaign->replicate();
        $newCampaign->name = $this->campaignData['name'];
        $newCampaign->image = $this->campaignData['image'];
        $newCampaign->og_image = null;
        $newCampaign->store_id = $this->campaignData['store_id']; 
        $newCampaign->save();

        $newCampaign->storefronts()->attach(
            $originalCampaign->storefronts->pluck('id')->toArray()
        );

        foreach ($originalCampaign->products as $originalProduct) {
            $newProduct = $originalProduct->replicate();
            $newProduct->campaign_id = $newCampaign->id;
            $newProduct->store_id = $newCampaign->store_id; 
            $newProduct->vendor_id = $originalProduct->vendor_id; 
            $newProduct->cover_image = $originalProduct->getRawOriginal('cover_image');
            $newProduct->hover_image = $originalProduct->getRawOriginal('hover_image');
            $newProduct->size_chart = $originalProduct->getRawOriginal('size_chart');
            $newProduct->og_image = $originalProduct->getRawOriginal('og_image');
            $newProduct->save();
            foreach ($originalProduct->variations as $variation) {
                $newProduct->variations()->create($variation->replicate()->toArray());
            }
            $newlyCreatedColors = [];
    
            foreach ($originalProduct->colors as $originalColor) {
                $newProductColor = $originalColor->replicate();
                $newProductColor->product_id = $newProduct->id;
                $newProductColor->save();
                $newlyCreatedColors[] = $newProductColor;
            }
            $newlyCreatedSides = [];
            foreach ($originalProduct->productSides as $originalSide) {
                $newSide = $originalSide->replicate();
                $newSide->product_id = $newProduct->id;
                $newSide->artwork = $this->campaignData['image'];
                $newSide->save();
                $newlyCreatedSides[] = $newSide; 
            }
            foreach ($newlyCreatedSides as $newSide) {
                foreach ($newlyCreatedColors as $newProductColor) {
                    GenerateMockupJob::dispatch(
                        $newSide->id,
                        $newProductColor->id,
                        $newProduct->id,
                        $newCampaign->id,
                        $this->campaignData['image']
                    );
                }
            }
        }
    }
}
